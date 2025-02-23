<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use App\Models\Konfigurasi;


class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $pelanggans = Pelanggan::all();
        return view('pelanggan.index', compact('pelanggans'));

        $query = Pelanggan::query();

    // Filter berdasarkan nama
    if ($request->has('nama') && !empty($request->nama)) {
        $query->where('nama', 'like', '%' . $request->nama . '%');
    }

    // Filter berdasarkan kategori tarif
    if ($request->has('kategori_tarif') && !empty($request->kategori_tarif)) {
        $query->where('kategori_tarif', $request->kategori_tarif);
    }

    $pelanggans = $query->get();

    return view('pelanggan.index', compact('pelanggans'));

    }
    
    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_pelanggan' => 'required|unique:pelanggans',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string',
            'tanggal_join' => 'required|date',
            'kwh_terakhir' => 'required|integer',
            'block_rumah' => 'required|string|max:50', // ✅ Validasi block rumah
            'kategori_tarif' => 'required|string|max:50', // ✅ Validasi kategori tarif
        ]);

        Pelanggan::create([
            'nomor_pelanggan' => $request->nomor_pelanggan,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'tanggal_join' => $request->tanggal_join,
            'tanggal_pembayaran_terakhir' => $request->tanggal_pembayaran_terakhir ?? $request->tanggal_join, // Default tanggal join jika kosong
            'kwh_terakhir' => $request->kwh_terakhir,
            'kwh_bulan_lalu' => 0, // ✅ Set nilai awal 0 untuk pelanggan baru
            'block_rumah' => $request->block_rumah,
            'kategori_tarif' => $request->kategori_tarif,
        ]);

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit($nomor_pelanggan)
    {
        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->firstOrFail();
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function show($nomor_pelanggan)
    {
        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->firstOrFail();

        // Ambil riwayat pembayaran pelanggan
        $pembayarans = Pembayaran::where('nomor_pelanggan', $nomor_pelanggan)
            ->orderBy('tanggal_pembayaran', 'desc')
            ->get();

        return view('pelanggan.show', compact('pelanggan', 'pembayarans'));
    }

    public function update(Request $request, $nomor_pelanggan)
    {
        $request->validate([
            'nomor_pelanggan' => 'required|unique:pelanggans',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string',
            'tanggal_join' => 'required|date',
            'kwh_terakhir' => 'required|integer',
            'block_rumah' => 'required|string|max:50',
            'kategori_tarif' => 'required|string|max:50',
        ]);

        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->firstOrFail();

        // Simpan KWH sebelumnya sebagai KWH Bulan Lalu sebelum update KWH Terakhir
        $pelanggan->kwh_bulan_lalu = $pelanggan->kwh_terakhir;
        $pelanggan->kwh_terakhir = $request->kwh_terakhir;
        $pelanggan->nama = $request->nama;
        $pelanggan->alamat = $request->alamat;
        $pelanggan->no_telepon = $request->no_telepon;
        $pelanggan->tanggal_join = $request->tanggal_join;
        $pelanggan->block_rumah = $request->block_rumah; // ✅ Update block rumah
        $pelanggan->kategori_tarif = $request->kategori_tarif; // ✅ Update kategori tarif

        $pelanggan->save();

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy($nomor_pelanggan)
    {
        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->firstOrFail();
        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function invoice($nomor_pelanggan) {
        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->firstOrFail();

        $konfigurasi = Konfigurasi::first();
        $tarif_per_kwh = json_decode($konfigurasi->tarif_per_kwh, true)[$pelanggan->kategori_tarif] ?? 1500;
        $denda_bulanan = $konfigurasi->denda_bulanan ?? 5000;

        $total_pemakaian = $pelanggan->kwh_terakhir - $pelanggan->kwh_bulan_lalu;
        $total_tagihan = $total_pemakaian * $tarif_per_kwh;

        $pembayaran = Pembayaran::where('nomor_pelanggan', $nomor_pelanggan)
            ->latest()
            ->first();

        $sudahTerbayar = $pembayaran && $pembayaran->jumlah_dibayar >= ($total_tagihan + $pembayaran->denda);

        if (!$sudahTerbayar) {
            $denda_baru = Pembayaran::hitungDenda($nomor_pelanggan);

            if ($pembayaran) {
                $pembayaran->update(['denda' => $denda_baru]);
            } else {
                $pembayaran = Pembayaran::create([
                    'nomor_pelanggan' => $pelanggan->nomor_pelanggan,
                    'tanggal_pembayaran' => null,
                    'jumlah_dibayar' => 0,
                    'denda' => $denda_baru,
                    'total_pemakaian' => $total_pemakaian,
                    'total_tagihan' => $total_tagihan,
                ]);
            }
        }

        return view('invoice.show', compact('pelanggan', 'pembayaran', 'total_pemakaian', 'total_tagihan', 'sudahTerbayar', 'denda_baru'));
    }
    
     
    
    
    


    public function updateTanggalPembayaranTerakhir($nomor_pelanggan, $tanggal_pembayaran)
    {
        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->firstOrFail();
        $pelanggan->update(['tanggal_pembayaran_terakhir' => $tanggal_pembayaran]);

        return response()->json(['message' => 'Tanggal pembayaran terakhir berhasil diperbarui'], 200);
    }

    public function history($nomor_pelanggan)
{
    $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->firstOrFail();
    
    // Ambil riwayat pembayaran pelanggan berdasarkan nomor pelanggan
    $pembayarans = Pembayaran::where('nomor_pelanggan', $nomor_pelanggan)
                    ->orderBy('tanggal_pembayaran', 'desc')
                    ->get();

    return view('pelanggan.history', compact('pelanggan', 'pembayarans'));
}

}
