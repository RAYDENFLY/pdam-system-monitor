<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    // Menampilkan semua tagihan
    public function index()
    {
        $tagihans = Tagihan::all();
        return view('tagihan.index', compact('tagihans'));
    }

    // Menampilkan form buat tagihan baru
    public function create()
    {
        return view('tagihan.create');
    }

    // Simpan tagihan baru
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bulan' => 'required|string',
            'harga_kwh' => 'required|numeric',
            'jumlah_kwh' => 'required|numeric',
        ]);

        // Ambil harga denda terbaru
        $hargaDenda = Tagihan::first()->harga_denda ?? 5000;

        // Cek apakah pengguna sudah memiliki tagihan di bulan yang sama
        $existingTagihan = Tagihan::where('user_id', $request->user_id)
                                  ->where('bulan', $request->bulan)
                                  ->first();

        if (!$existingTagihan) {
            // Buat tagihan baru
            Tagihan::create([
                'user_id' => $request->user_id,
                'bulan' => $request->bulan,
                'harga_kwh' => $request->harga_kwh,
                'jumlah_kwh' => $request->jumlah_kwh,
                'total_bayar' => $request->harga_kwh * $request->jumlah_kwh,
                'denda_tercatat' => null, // Denda belum dikenakan
                'status' => 'belum bayar',
            ]);
        }

        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil dibuat.');
    }

    // Menampilkan form edit tagihan
    public function edit($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        return view('tagihan.edit', compact('tagihan'));
    }

    // Update tagihan
    public function update(Request $request, $id)
    {
        $tagihan = Tagihan::findOrFail($id);

        $request->validate([
            'harga_kwh' => 'required|numeric',
            'jumlah_kwh' => 'required|numeric',
        ]);

        $tagihan->update([
            'harga_kwh' => $request->harga_kwh,
            'jumlah_kwh' => $request->jumlah_kwh,
            'total_bayar' => $request->harga_kwh * $request->jumlah_kwh,
        ]);

        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil diperbarui.');
    }

    // Hapus tagihan
    public function destroy($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $tagihan->delete();

        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil dihapus.');
    }

    // Kenakan denda jika pengguna belum bayar
    public function kenakanDenda($userId, $bulan)
    {
        $tagihan = Tagihan::where('user_id', $userId)->where('bulan', $bulan)->first();

        if ($tagihan && $tagihan->status == 'belum bayar') {
            // Jika denda belum dikenakan sebelumnya, gunakan harga denda saat ini
            if ($tagihan->denda_tercatat === null) {
                $hargaDenda = Tagihan::first()->harga_denda ?? 5000;
                $tagihan->denda_tercatat = $hargaDenda;
            } else {
                // Jika sudah kena denda sebelumnya, tambahkan denda dengan tarif terbaru
                $hargaDendaBaru = Tagihan::first()->harga_denda ?? 5000;
                $tagihan->denda_tercatat += $hargaDendaBaru;
            }

            // Update total bayar
            $tagihan->total_bayar += $tagihan->denda_tercatat;
            $tagihan->save();
        }
    }

    // Update harga denda dan harga kWh oleh admin
    public function updatePricing(Request $request)
    {
        $request->validate([
            'harga_denda' => 'required|numeric',
            'harga_kwh' => 'required|numeric',
        ]);

        // Ambil harga denda sebelum diubah
        $hargaDendaLama = Tagihan::first()->harga_denda ?? 5000;

        // Update harga terbaru
        Tagihan::updateOrCreate([], [
            'harga_denda' => $request->harga_denda,
            'harga_kwh' => $request->harga_kwh,
        ]);

        // Pastikan tagihan lama tetap memakai harga denda lama
        \DB::table('tagihans')->whereNull('denda_tercatat')->update([
            'denda_tercatat' => $hargaDendaLama,
        ]);

        return redirect()->back()->with('success', 'Harga berhasil diperbarui.');
    }

    // Menampilkan halaman edit harga
    public function editHarga()
    {
        $tagihan = Tagihan::first();

        return view('admin.harga', [
            'hargaDenda' => $tagihan->harga_denda ?? 5000,
            'hargaKwh' => $tagihan->harga_kwh ?? 0,
        ]);
    }
}
