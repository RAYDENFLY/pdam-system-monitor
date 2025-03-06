<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CatatanPemakaian;
use App\Models\Pelanggan;

class CatatanPemakaianController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::all();
        return view('pelanggan.catat', compact('pelanggans'));
    }
    public function store(Request $request)
{
    // Ambil pelanggan berdasarkan nomor_pelanggan
    $pelanggan = Pelanggan::where('nomor_pelanggan', $request->nomor_pelanggan)->firstOrFail();
    
    // Set penggunaan_awal berdasarkan kwh_terakhir pelanggan
    $penggunaan_awal = $pelanggan->kwh_terakhir;

    // Gabungkan bulan dan tahun pemakaian
    $periode_pemakaian = $request->tahun_pemakaian . '-' . str_pad(array_search($request->bulan_pemakaian, [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ]) + 1, 2, '0', STR_PAD_LEFT); 

    // Validasi input
    $request->validate([
        'penggunaan_akhir' => 'required|numeric|min:0|gte:' . $penggunaan_awal,
        'bulan_pemakaian' => 'required|string',
        'tahun_pemakaian' => 'required|numeric|min:2000|max:' . date('Y'),
    ]);

    // Tentukan tanggal batas pembayaran (misal tanggal 10 bulan berikutnya)
    $tanggal_batas_pembayaran = date('Y-m-d', strtotime($periode_pemakaian . '-10'));

    // Simpan catatan pemakaian
    CatatanPemakaian::create([
        'nomor_pelanggan' => $pelanggan->nomor_pelanggan,
        'penggunaan_awal' => $penggunaan_awal,
        'penggunaan_akhir' => $request->penggunaan_akhir,
        'jumlah_penggunaan' => $request->penggunaan_akhir - $penggunaan_awal,
        'periode_pemakaian' => $periode_pemakaian,
        'tanggal_batas_pembayaran' => $tanggal_batas_pembayaran, // Auto-set tanggal pembayaran
    ]);

    // Update kwh_terakhir pelanggan dengan penggunaan_akhir
    $pelanggan->update([
        'kwh_terakhir' => $request->penggunaan_akhir
    ]);

    return redirect()->route('pelanggan.catat')->with('success', 'Data pemakaian berhasil disimpan.');
}

    
    
}
