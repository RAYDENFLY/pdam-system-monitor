<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;

class LaporanKeuanganController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tanggal dari filter, default bulan ini
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());

        // Ambil data pembayaran berdasarkan rentang tanggal
        $pembayarans = Pembayaran::whereBetween('tanggal_pembayaran', [$tanggal_mulai, $tanggal_selesai])->get();

        // Hitung total pemasukan
        $total_pemasukan = $pembayarans->sum('jumlah_dibayar');

        return view('laporan.index', compact('pembayarans', 'total_pemasukan', 'tanggal_mulai', 'tanggal_selesai'));
    }
}
