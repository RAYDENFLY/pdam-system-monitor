<?php

namespace App\Http\Controllers;

use App\Models\Konfigurasi;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Pembayaran::with('pelanggan'); // Pastikan pelanggan terhubung
    
        if ($search) {
            $query->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('nomor_pelanggan', 'like', "%$search%")
                    ->orWhere('nama', 'like', "%$search%");
            });
        }
    
        $pembayarans = $query->get()->map(function ($pembayaran) {
            // Pastikan denda tidak negatif
            $pembayaran->denda = max(0, $pembayaran->denda);
            return $pembayaran;
        });
    
        return view('pembayaran.index', compact('pembayarans'));
    }

    public function store(Request $request, $nomor_pelanggan)
    {
        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->firstOrFail();
        $konfigurasi = Konfigurasi::first();
    
        $request->validate([
            'jumlah_dibayar' => 'required|integer|min:0',
        ]);
    
        $tanggal_pembayaran = now(); 
        $total_pemakaian = max(0, $pelanggan->kwh_terakhir - $pelanggan->kwh_bulan_lalu);
    
        $tarif_per_kwh = json_decode($konfigurasi->tarif_per_kwh, true) ?? [];
        $tarif_kwh_pelanggan = $tarif_per_kwh[$pelanggan->kategori_tarif] ?? 1500;

        $biaya_admin = $konfigurasi->biaya_admin ?? 0;
        $biaya_abodemen = $konfigurasi->biaya_abodemen ?? 0;
        $denda = ($tanggal_pembayaran->day > 20) ? ($konfigurasi->denda_bulanan ?? 5000) : 0;
        $total_tagihan = ($total_pemakaian * $tarif_kwh_pelanggan) + $denda + $biaya_admin + $biaya_abodemen;
    
        Pembayaran::create([
            'nomor_pelanggan' => $pelanggan->nomor_pelanggan,
            'total_pemakaian' => $total_pemakaian,
            'total_tagihan' => $total_tagihan,
            'jumlah_dibayar' => $request->jumlah_dibayar,
            'tanggal_pembayaran' => $tanggal_pembayaran,
            'denda' => $denda,
            'biaya_admin' => $biaya_admin,
            'biaya_abodemen' => $biaya_abodemen,
        ]);
    
        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil dicatat.');
    }
}
