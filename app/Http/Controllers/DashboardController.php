<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use Carbon\Carbon;
use App\Models\Pengeluaran; 

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
        // ✅ Total semua pengeluaran bulan ini (dari tabel pengeluarans)
$pengeluaranBulanIni = Pengeluaran::where('tanggal', 'like', "$bulanIni%")->sum('jumlah');

        // ✅ Jumlah pelanggan yang belum bayar bulan ini
        $jumlahBelumBayar = Pelanggan::whereDoesntHave('pembayarans', function ($query) use ($bulanIni) {
            $query->where('tanggal_pembayaran', 'like', "$bulanIni%");
        })->count();

        $jumlahInvoiceBelumTerbayar = Pembayaran::where('tanggal_pembayaran', 'like', "$bulanIni%")
        ->get()
        ->filter(function($pembayaran) {
            return !$pembayaran->isLunas();
        })
        ->count();
    

// ✅ Daftar Invoice yang Belum Terbayar
$invoicesBelumTerbayar = Pembayaran::where('tanggal_pembayaran', 'like', "$bulanIni%")
    ->get()
    ->filter(function($pembayaran) {
        return !$pembayaran->isLunas();
    });

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
