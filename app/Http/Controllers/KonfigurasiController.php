<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Konfigurasi;

class KonfigurasiController extends Controller
{
    public function index()
    {
        $konfigurasi = Konfigurasi::first();
        
        // Pastikan tarif_per_kwh bisa dibaca sebagai array
        if ($konfigurasi) {
            $konfigurasi->tarif_per_kwh = json_decode($konfigurasi->tarif_per_kwh, true);
        }

        return view('admin.konfigurasi', compact('konfigurasi'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'denda_bulanan' => 'required|integer|min:0',
            'tarif_per_kwh' => 'required|array',
        ]);

        $konfigurasi = Konfigurasi::firstOrCreate([]);

        $konfigurasi->denda_bulanan = $request->denda_bulanan;
        $konfigurasi->tarif_per_kwh = json_encode($request->tarif_per_kwh);
        $konfigurasi->save();

        return redirect()->route('konfigurasi.index')->with('success', 'Konfigurasi berhasil diperbarui.');
    }
}
