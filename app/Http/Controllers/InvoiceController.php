<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Konfigurasi;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::all();
        return view('invoice.index', compact('pelanggans'));
    }

    public function show($nomor_pelanggan)
    {
        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->firstOrFail();
        
        // Ambil pembayaran terakhir
        $pembayaranTerakhir = Pembayaran::where('nomor_pelanggan', $nomor_pelanggan)
            ->latest()
            ->first();
    
        // Ambil konfigurasi

        $konfigurasi = Konfigurasi::first();
        $tarif_per_kwh = $konfigurasi->tarif_per_kwh[$pelanggan->kategori_tarif] ?? 1500;
        
        // Hitung total
        $total_pemakaian = max(0, $pelanggan->kwh_terakhir - $pelanggan->kwh_bulan_lalu);
        $total_tagihan = $total_pemakaian * $tarif_per_kwh;
        
        // Pastikan denda selalu positif
        $total_denda = abs(Pembayaran::hitungDenda($nomor_pelanggan));
    
        // Pastikan total pembayaran dihitung dengan penambahan
        $totalPembayaran = $total_tagihan + $total_denda;

      
        $tarif_daya = $pelanggan->kategori_tarif . ' / ' . $tarif_per_kwh . ' per kWh';

    
        // Debug untuk melihat nilai-nilai
        \Log::info("Debug Nilai di show():");
        \Log::info("Total Tagihan: " . $total_tagihan);
        \Log::info("Total Denda: " . $total_denda);
        \Log::info("Total Pembayaran: " . $totalPembayaran);
    
        return view('invoice.show', compact(
            'pelanggan',
            'pembayaranTerakhir',
            'total_pemakaian',
            'total_tagihan',
            'total_denda',
            'totalPembayaran',
            'tarif_daya'
        ));
    }

    public function generateAll()
    {
        $konfigurasi = Konfigurasi::first();
        $pelanggans = Pelanggan::all();
        $invoices = [];

        foreach ($pelanggans as $pelanggan) {
            $tarif_per_kwh = $konfigurasi->tarif_per_kwh[$pelanggan->kategori_tarif] ?? 1500;
            $total_pemakaian = max(0, $pelanggan->kwh_terakhir - $pelanggan->kwh_bulan_lalu);
            $total_tagihan = $total_pemakaian * $tarif_per_kwh;
            $total_denda = Pembayaran::hitungDenda($pelanggan->nomor_pelanggan);

            $pembayaranTerakhir = Pembayaran::where('nomor_pelanggan', $pelanggan->nomor_pelanggan)
                ->latest()
                ->first();

            // Perbaikan pengecekan sudah terbayar
            $total_yang_harus_dibayar = $total_tagihan + $total_denda;
            $sudahTerbayar = $pembayaranTerakhir && $pembayaranTerakhir->jumlah_dibayar >= $total_yang_harus_dibayar;
            
            
            if (!$sudahTerbayar) {
                $invoices[] = [
                    'pelanggan' => $pelanggan,
                    'total_pemakaian' => $total_pemakaian,
                    'total_tagihan' => $total_tagihan,
                    'total_denda' => $total_denda,
                    'total_pembayaran' => $total_yang_harus_dibayar,
                    'pembayaran' => $pembayaranTerakhir
                ];
            }
        }

        return view('invoice.automatic', compact('invoices'));
    }

    public function updateStatus($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        // Update jumlah dibayar (lunas) dengan total yang benar
        $totalPembayaran = $pembayaran->total_tagihan + $pembayaran->denda;
        $pembayaran->jumlah_dibayar = $totalPembayaran;
        $pembayaran->tanggal_pembayaran = now();
        $pembayaran->save();
    
        // Update pelanggan
        $pelanggan = $pembayaran->pelanggan;
        $pelanggan->tanggal_pembayaran_terakhir = now();
        $pelanggan->save();
    
        return redirect()->back()->with('success', 'Invoice berhasil ditandai sebagai lunas.');
    }

    public function markUnpaid($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('invoice.list')->with('error', 'Anda tidak memiliki izin.');
        }

        $invoice = Pembayaran::findOrFail($id);
        $invoice->jumlah_dibayar = 0;
        $invoice->save();

        return redirect()->route('invoice.list')->with('success', 'Invoice berhasil ditandai sebagai belum lunas.');
    }

    public function markPaidIndex(Request $request)
    {
        $search = $request->input('search');
        $query = Pelanggan::with(['pembayarans' => function ($q) {
            $q->latest();
        }]);

        if ($search) {
            $query->where('nama', 'like', "%$search%")
                ->orWhere('nomor_pelanggan', 'like', "%$search%");
        }

        $pelanggans = $query->get()->map(function ($pelanggan) {
            $pembayaranTerakhir = $pelanggan->pembayarans->first();
            // Perbaikan logika pengecekan lunas
            $total_yang_harus_dibayar = $pembayaranTerakhir ? 
                ($pembayaranTerakhir->total_tagihan + $pembayaranTerakhir->denda) : 0;
            
            $pelanggan->status_pembayaran = ($pembayaranTerakhir && $pembayaranTerakhir->jumlah_dibayar >= $total_yang_harus_dibayar)
                ? 'lunas'
                : 'belum lunas';

            return $pelanggan;
        });

        return view('invoice.mark_paid', compact('pelanggans'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::all();
        return view('invoice.create', compact('pelanggans'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nomor_pelanggan' => 'required|exists:pelanggans,nomor_pelanggan',
            'total_pemakaian' => 'required|numeric|min:0',
            'total_tagihan' => 'required|numeric|min:0',
            'biaya_admin' => 'nullable|numeric|min:0', // Bisa kosong, default 2500
            'biaya_abodemen' => 'nullable|numeric|min:0', // Bisa kosong
            'denda' => 'nullable|numeric',
        ]);

        if (!$request->has('denda')) {
            $validated['denda'] = Pembayaran::hitungDenda($request->nomor_pelanggan);
        }
    
        // Ambil pelanggan
        $pelanggan = Pelanggan::where('nomor_pelanggan', $request->nomor_pelanggan)->firstOrFail();
    
        // Hitung denda - make sure we get a positive value
        $total_denda = max(0, Pembayaran::hitungDenda($request->nomor_pelanggan));
    
        // Log the denda calculation
        \Log::info("Creating invoice for customer: " . $request->nomor_pelanggan);
        \Log::info("Calculated denda: " . $total_denda);
    
        // Ambil biaya admin, default ke 2500 jika tidak diisi
        $biaya_admin = $request->biaya_admin ?? 2500;
        $biaya_abodemen = $request->biaya_abodemen ?? 0;
    
        // Hitung total pembayaran
        $total_pembayaran = $request->total_tagihan + $total_denda + $biaya_admin + $biaya_abodemen;
    
        // Simpan pembayaran baru
        $pembayaran = Pembayaran::create([
            'nomor_pelanggan' => $request->nomor_pelanggan,
            'total_pemakaian' => $request->total_pemakaian,
            'total_tagihan' => $request->total_tagihan,
            'biaya_admin' => $biaya_admin,
            'biaya_abodemen' => $biaya_abodemen,
            'jumlah_dibayar' => 0,  // Belum dibayar
            'tanggal_pembayaran' => null, // Belum dibayar
            'denda' => $total_denda,  // Make sure this is saved
        ]);
        dd([
            'nomor_pelanggan' => $request->nomor_pelanggan,
            'kwh_terakhir' => $request->kwh_terakhir ?? 'Tidak ada',
            'kwh_bulan_lalu' => $request->kwh_bulan_lalu ?? 'Tidak ada',
            'total_pemakaian' => $request->total_pemakaian,
            'total_tagihan' => $request->total_tagihan,
            'denda' => $total_denda
        ]);
        
        
        
        // Double-check the save was successful
        \Log::info("Saved invoice with ID: " . $pembayaran->id);
        \Log::info("Saved denda value: " . $pembayaran->denda);
        Pembayaran::create($validated);
        
        return redirect()->route('invoice.list')->with('success', 'Invoice berhasil dibuat.');
    }
    public function destroy($id)
{
    $invoice = Pembayaran::findOrFail($id);

    // Pastikan hanya admin yang bisa menghapus
    if (auth()->user()->role !== 'admin') {
        return redirect()->route('invoice.list')->with('error', 'Anda tidak memiliki izin untuk menghapus invoice.');
    }

    $invoice->delete();
    return redirect()->route('invoice.list')->with('success', 'Invoice berhasil dihapus.');
}

public function list(Request $request)
{
    $search = $request->input('search');
    $query = Pembayaran::with('pelanggan');

    if ($search) {
        $query->whereHas('pelanggan', function ($q) use ($search) {
            $q->where('nomor_pelanggan', 'like', "%$search%")
                ->orWhere('nama', 'like', "%$search%");
        });
    }

    $invoices = $query->get();
    foreach ($invoices as $invoice) {
        \Log::info("Invoice ID: " . $invoice->id . ", Denda: " . $invoice->denda);
    }
    
    return view('invoice.list', compact('invoices'));
}
    
    public function getDenda($nomor_pelanggan)
    {
        $total_denda = Pembayaran::hitungDenda($nomor_pelanggan);
        return response()->json(['denda' => $total_denda]);
    }
}