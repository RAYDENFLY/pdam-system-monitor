<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil tanggal bulan ini
        $bulanIni = Carbon::now()->format('Y-m');

        // ✅ Jumlah total pelanggan
        $jumlahPelanggan = Pelanggan::count();

        // ✅ Total tagihan dalam periode bulan ini (semua invoice yang dibuat)
        $totalTagihanBulanIni = Pembayaran::where('tanggal_pembayaran', 'like', "$bulanIni%")->sum('total_tagihan');

        // ✅ Pendapatan bulan ini (hanya invoice yang sudah dibayar)
        $pendapatanBulanIni = Pembayaran::where('tanggal_pembayaran', 'like', "$bulanIni%")
                                    ->whereColumn('jumlah_dibayar', '>=', 'total_tagihan')
                                    ->sum('jumlah_dibayar');

        // ✅ Pengeluaran (contoh, jika ada tabel pengeluaran bisa diambil dari database)
        $pengeluaranBulanIni = 500000; // Bisa diganti dengan query jika ada tabel pengeluaran

        // ✅ Jumlah pelanggan yang belum bayar bulan ini
        $jumlahBelumBayar = Pelanggan::whereDoesntHave('pembayarans', function ($query) use ($bulanIni) {
            $query->where('tanggal_pembayaran', 'like', "$bulanIni%");
        })->count();

        // ✅ Jumlah invoice belum terbayar (tagihan yang belum lunas)
        $jumlahInvoiceBelumTerbayar = Pembayaran::where('tanggal_pembayaran', 'like', "$bulanIni%")
                                            ->whereColumn('jumlah_dibayar', '<', 'total_tagihan')
                                            ->count();

        // ✅ Daftar Invoice yang Belum Terbayar
        $invoicesBelumTerbayar = Pembayaran::where('tanggal_pembayaran', 'like', "$bulanIni%")
                                           ->whereColumn('jumlah_dibayar', '<', 'total_tagihan')
                                           ->get();

        return view('dashboard.index', compact(
            'jumlahPelanggan', 
            'totalTagihanBulanIni', 
            'pendapatanBulanIni', 
            'pengeluaranBulanIni', 
            'jumlahBelumBayar', 
            'jumlahInvoiceBelumTerbayar',
            'invoicesBelumTerbayar'
        ));
    }
}
