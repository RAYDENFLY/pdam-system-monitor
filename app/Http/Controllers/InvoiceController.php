<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    // Menampilkan daftar pelanggan untuk dipilih
    public function index()
    {
        $pelanggans = Pelanggan::all();
        return view('invoice.index', compact('pelanggans'));
    }

    // Menampilkan invoice pelanggan tertentu
    public function show($nomor_pelanggan)
    {
        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->firstOrFail();
    
        // Ambil pembayaran terakhir pelanggan
        $pembayaran = Pembayaran::where('nomor_pelanggan', $nomor_pelanggan)
                        ->latest()
                        ->first();
    
        // Jika tidak ada pembayaran, buat objek kosong agar tidak error
        if (!$pembayaran) {
            $pembayaran = new Pembayaran([
                'jumlah_dibayar' => 0,
                'total_tagihan' => 0,
                'denda' => 0,
                'tanggal_pembayaran' => null
            ]);
        }
    
        // Hitung total pemakaian dan tagihan
        $tarif_per_kwh = 1500;
        $total_pemakaian = $pelanggan->kwh_terakhir - $pelanggan->kwh_bulan_lalu;
        $total_tagihan = $total_pemakaian * $tarif_per_kwh;
    
        // Tentukan apakah invoice sudah dibayar atau belum
        $sudahTerbayar = $pembayaran->jumlah_dibayar >= $total_tagihan;
    
        return view('invoice.show', compact(
            'pelanggan',
            'pembayaran',
            'total_pemakaian',
            'total_tagihan',
            'sudahTerbayar'
        ));
    }
    
    


    // ✅ Generate Invoice Otomatis untuk Semua Pelanggan yang Belum Bayar
    public function generateAll()
    {
        $pelanggans = Pelanggan::all();
        $invoices = [];

        foreach ($pelanggans as $pelanggan) {
            // Periksa apakah pelanggan belum membayar bulan ini
            $pembayaranTerakhir = Pembayaran::where('nomor_pelanggan', $pelanggan->nomor_pelanggan)
                                    ->latest()
                                    ->first();

            // Hitung total pemakaian & tagihan
            $tarif_per_kwh = 1500;
            $total_pemakaian = $pelanggan->kwh_terakhir - $pelanggan->kwh_bulan_lalu;
            $total_tagihan = $total_pemakaian * $tarif_per_kwh;

            // Cek apakah pembayaran sudah lunas atau belum
            $sudahTerbayar = $pembayaranTerakhir && $pembayaranTerakhir->jumlah_dibayar >= $total_tagihan;

            if (!$sudahTerbayar) {
                $invoices[] = [
                    'pelanggan' => $pelanggan,
                    'total_pemakaian' => $total_pemakaian,
                    'total_tagihan' => $total_tagihan,
                    'pembayaran' => $pembayaranTerakhir
                ];
            }
        }

        return view('invoice.automatic', compact('invoices'));
    }

    public function updateStatus($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        // Tandai invoice sebagai lunas
        $pembayaran->jumlah_dibayar = $pembayaran->total_tagihan;
        $pembayaran->save();
    
        return redirect()->back()->with('success', 'Invoice berhasil ditandai sebagai lunas.');
    }
    
    public function markPaid($id)
    {
        // Ambil invoice berdasarkan ID
        $invoice = Pembayaran::findOrFail($id);
    
        // Tandai invoice sebagai lunas
        $invoice->jumlah_dibayar = $invoice->total_tagihan;
        $invoice->save();
    
        return redirect()->route('invoice.list')->with('success', 'Invoice berhasil ditandai sebagai lunas.');
    }
    public function markUnpaid($id)
{
    // Pastikan hanya admin yang bisa mengakses
    if (auth()->user()->role !== 'admin') {
        return redirect()->route('invoice.list')->with('error', 'Anda tidak memiliki izin.');
    }

    // Ambil invoice berdasarkan ID
    $invoice = Pembayaran::findOrFail($id);

    // Tandai invoice sebagai belum lunas
    $invoice->jumlah_dibayar = 0;
    $invoice->save();

    return redirect()->route('invoice.list')->with('success', 'Invoice berhasil ditandai sebagai belum lunas.');
}
    
    
    public function markPaidIndex(Request $request)
    {
        // Ambil query pencarian & filter status
        $search = $request->input('search');
        $status = $request->input('status');
    
        // Query daftar pelanggan dengan pembayaran terakhir
        $query = Pelanggan::with(['pembayarans' => function ($q) {
            $q->latest();
        }]);
    
        // Filter berdasarkan pencarian nama atau nomor pelanggan
        if ($search) {
            $query->where('nama', 'like', "%$search%")
                  ->orWhere('nomor_pelanggan', 'like', "%$search%");
        }
    
        // Filter berdasarkan status pembayaran
        $pelanggans = $query->get()->map(function ($pelanggan) {
            $pembayaranTerakhir = $pelanggan->pembayarans->first();
    
            // Tentukan status pembayaran
            $pelanggan->status_pembayaran = ($pembayaranTerakhir && $pembayaranTerakhir->jumlah_dibayar >= $pembayaranTerakhir->total_tagihan) ? 'lunas' : 'belum lunas';
    
            return $pelanggan;
        });
    
        return view('invoice.mark_paid', compact('pelanggans'));
    }
    


public function create()
{
    $pelanggans = Pelanggan::all(); // Ambil daftar pelanggan untuk dipilih
    return view('invoice.create', compact('pelanggans'));
}

public function store(Request $request)
{
    $request->validate([
        'nomor_pelanggan' => 'required',
        'kwh_bulan_lalu' => 'required|integer',
        'kwh_terakhir' => 'required|integer',
        'total_pemakaian' => 'required|integer',
        'total_tagihan' => 'required|integer',
        'denda' => 'required|integer',
        'total_pembayaran' => 'required|integer',
    ]);

    Pembayaran::create([
        'nomor_pelanggan' => $request->nomor_pelanggan,
        'total_pemakaian' => $request->total_pemakaian,
        'total_tagihan' => $request->total_tagihan,
        'jumlah_dibayar' => 0, // Default belum dibayar
        'tanggal_pembayaran' => now(),
        'denda' => $request->denda,
    ]);

    return redirect()->route('invoice.create')->with('success', 'Invoice berhasil dibuat.');
}

public function list(Request $request)
{
    // Ambil query pencarian & filter status
    $search = $request->input('search');
    $status = $request->input('status');

    // Query daftar invoice dengan relasi pelanggan
    $query = Pembayaran::with('pelanggan');

    // Filter berdasarkan pencarian nomor pelanggan atau nama
    if ($search) {
        $query->whereHas('pelanggan', function ($q) use ($search) {
            $q->where('nomor_pelanggan', 'like', "%$search%")
              ->orWhere('nama', 'like', "%$search%");
        });
    }

    // Filter berdasarkan status pembayaran
    if ($status === 'lunas') {
        $query->whereColumn('jumlah_dibayar', '>=', 'total_tagihan');
    } elseif ($status === 'belum') {
        $query->whereColumn('jumlah_dibayar', '<', 'total_tagihan');
    }

    // Ambil data invoice
    $invoices = $query->get();

    return view('invoice.list', compact('invoices'));
}




}
