<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Konfigurasi; // Pastikan model ini ada

class KonfigurasiController extends Controller
{
    public function index()
    {
        // Ambil data konfigurasi pertama (gunakan first() karena biasanya hanya satu baris)
        $konfigurasi = Konfigurasi::first();
        return view('admin.konfigurasi', compact('konfigurasi'));
    }

    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'denda_bulanan' => 'required|numeric',
            'tarif_per_kwh.R1' => 'required|numeric',
            'tarif_per_kwh.R2' => 'required|numeric',
            'tarif_per_kwh.R3' => 'required|numeric',
            'tarif_per_kwh.B1' => 'required|numeric',
        ]);

        // Ambil data konfigurasi (jika tidak ada, buat baru)
        $konfigurasi = Konfigurasi::firstOrNew([]);

        // Update data dari request
        $konfigurasi->denda_bulanan = $request->input('denda_bulanan');
        $konfigurasi->tarif_per_kwh = json_encode($request->input('tarif_per_kwh')); // Simpan sebagai JSON

        // Simpan perubahan
        $konfigurasi->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('konfigurasi.index')->with('success', 'Konfigurasi berhasil diperbarui!');
    }
}
