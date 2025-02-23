<?php

namespace App\Http\Controllers;
use App\Models\Konfigurasi;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayarans = Pembayaran::latest()->get();
        return view('pembayaran.index', compact('pembayarans'));
    }

    public function store(Request $request, $nomor_pelanggan)
    {
        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->firstOrFail();
    
        $request->validate([
            'jumlah_dibayar' => 'required|integer|min:0',
        ]);
    
        // Gunakan tanggal saat ini sebagai tanggal pembayaran
        $tanggal_pembayaran = now(); 
    
        // Hitung total pemakaian dan tagihan
        $total_pemakaian = max(0, $pelanggan->kwh_terakhir - $pelanggan->kwh_bulan_lalu);

        // Pastikan tarif_per_kwh adalah array, jika null, gunakan default array kosong
        $tarif_per_kwh = is_array($konfigurasi->tarif_per_kwh) ? $konfigurasi->tarif_per_kwh : [];
        
        $tarif_kwh_pelanggan = $tarif_per_kwh[$pelanggan->kategori_tarif] ?? 1500;
        $total_tagihan = $total_pemakaian * $tarif_kwh_pelanggan;
        
    
        // Cek apakah pembayaran dilakukan setelah tanggal 20
        $konfigurasi = Konfigurasi::first();
        $denda = ($tanggal_pembayaran->day > 20) ? ($konfigurasi->denda_bulanan ?? 5000) : 0;        

    
        // Simpan data pembayaran dengan denda otomatis
        Pembayaran::create([
            'nomor_pelanggan' => $pelanggan->nomor_pelanggan,
            'total_pemakaian' => $total_pemakaian,
            'total_tagihan' => $total_tagihan,
            'jumlah_dibayar' => $request->jumlah_dibayar,
            'tanggal_pembayaran' => $tanggal_pembayaran, 
            'denda' => $denda, 
        ]);
    
        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil dicatat.');
    }
    
    
}
