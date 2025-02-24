<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Konfigurasi;
use Carbon\Carbon;
use App\Models\Abodemen;

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

        $biaya_admin = $konfigurasi->biaya_admin;
        $abodemen = $konfigurasi->abodemen;
    
        // Hitung total
        $total_pemakaian = max(0, $pelanggan->kwh_terakhir - $pelanggan->kwh_bulan_lalu);
        $total_tagihan = $total_pemakaian * $tarif_per_kwh;
        
        // Pastikan denda selalu positif
        $total_denda = abs(Pembayaran::hitungDenda($nomor_pelanggan));

         // Pastikan biaya admin dan abodemen menggunakan nilai yang sudah tersimpan di invoice, 
        // agar invoice lama tidak berubah ketika admin memperbarui konfigurasi
        $biaya_admin = $pembayaranTerakhir->biaya_admin ?? $konfigurasi->biaya_admin;
        // Ambil abodemen dari tabel abodemens, jika tidak ada pakai default
        $biaya_langganan = Abodemen::where('nomor_pelanggan', $nomor_pelanggan)->value('abodemen');
    
        // Pastikan total pembayaran dihitung dengan penambahan
        $totalPembayaran = $total_tagihan + $total_denda + $biaya_admin + $abodemen;
    
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
            'biaya_admin', 
            'biaya_langganan',
            'totalPembayaran'
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
            $biaya_admin = $konfigurasi->biaya_admin;
            $abodemen = $konfigurasi->abodemen;

            $pembayaranTerakhir = Pembayaran::where('nomor_pelanggan', $pelanggan->nomor_pelanggan)
                ->latest()
                ->first();

            // Perbaikan pengecekan sudah terbayar
            $total_yang_harus_dibayar = $total_tagihan + $total_denda + $biaya_admin + $abodemen;
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
        $konfigurasi = Konfigurasi::first(); // Ambil konfigurasi dari database
        // Default denda jika tidak ada pelanggan yang dipilih
         $total_denda = 0; 
        return view('invoice.create', compact('pelanggans', 'konfigurasi'));
    }

    public function store(Request $request)
    {
        $pelanggan = Pelanggan::where('nomor_pelanggan', $request->nomor_pelanggan)->firstOrFail();
        $total_denda = max(0, Pembayaran::hitungDenda($request->nomor_pelanggan)); 
        $konfigurasi = Konfigurasi::first();
    
        // Debug untuk melihat nilai yang diterima
        \Log::info('Request data:', [
            'custom_biaya_langganan' => $request->input('custom_biaya_langganan'),
            'biaya_langganan_value' => $request->input('biaya_langganan'),
        ]);
    
        // Perbaikan logika pengecekan
        $biaya_admin = $request->filled('custom_biaya_admin') ? (int)$request->input('biaya_admin') : $konfigurasi->biaya_admin;
        $biaya_langganan = Abodemen::where('nomor_pelanggan', $nomor_pelanggan)->value('abodemen') ?? $konfigurasi->abodemen;
    
        $total_pembayaran = $request->total_tagihan + $total_denda + $biaya_admin + $biaya_langganan;
    
        // Debug total pembayaran
        \Log::info('Calculated values:', [
            'total_tagihan' => $request->total_tagihan,
            'total_denda' => $total_denda,
            'biaya_admin' => $biaya_admin,
            'biaya_langganan' => $biaya_langganan,
            'total_pembayaran' => $total_pembayaran
        ]);
    
        $pembayaran = Pembayaran::create([
            'nomor_pelanggan' => $request->nomor_pelanggan,
            'total_pemakaian' => $request->total_pemakaian,
            'total_tagihan' => $request->total_tagihan,
            'jumlah_dibayar' => 0,
            'tanggal_pembayaran' => null,
            'denda' => $total_denda,
            'biaya_admin' => $biaya_admin,
            'biaya_langganan' => $biaya_langganan, // Ubah dari abodemen ke biaya_langganan
        ]);
    
        return redirect()->route('invoice.list')->with('success', 'Invoice berhasil dibuat.');
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
    
        $invoices = $query->get()->map(function ($invoice) {
            // Pastikan denda tidak negatif
            $invoice->denda = max(0, $invoice->denda);
        
            // Pastikan biaya_admin dan biaya_langganan ada
            $invoice->biaya_admin = $invoice->biaya_admin ?? 0;
            $invoice->biaya_langganan = $invoice->biaya_langganan ?? 0;
        
            // Hitung total pembayaran
            $invoice->total_pembayaran = $invoice->total_tagihan + $invoice->denda + $invoice->biaya_admin + $invoice->biaya_langganan;
        
            return $invoice;
        });
        
    
        return view('invoice.list', compact('invoices'));
    }
    
    public function getDenda($nomor_pelanggan)
    {
        $total_denda = Pembayaran::hitungDenda($nomor_pelanggan);
        return response()->json(['denda' => $total_denda]);
    }
}
