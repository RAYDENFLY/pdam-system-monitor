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
        $request->validate([
            'biaya_admin' => 'required|integer|min:0',
            'abodemen' => 'required|integer|min:0',
            'denda_bulanan' => 'required|integer|min:0',
            'tarif_per_kwh' => 'required|array',
        ]);
    
        $konfigurasi = Konfigurasi::first();
        $konfigurasi->biaya_admin = $request->biaya_admin;
        $konfigurasi->abodemen = $request->abodemen;
        $konfigurasi->denda_bulanan = $request->denda_bulanan;
        $konfigurasi->tarif_per_kwh = $request->tarif_per_kwh;
        $konfigurasi->save();
    
        return redirect()->route('konfigurasi.index')->with('success', 'Konfigurasi berhasil diperbarui.');
    }
    

    
    
}
