<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Konfigurasi;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use App\Models\Pengeluaran;


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

          // Ambil data pengeluaran (Pengeluaran)
            $pengeluarans = Pengeluaran::whereBetween('tanggal', [$tanggal_mulai, $tanggal_selesai])->get();
            $total_pengeluaran = $pengeluarans->sum('jumlah');


        return view('laporan.index', compact('pembayarans', 'total_pemasukan', 'tanggal_mulai', 'tanggal_selesai','pengeluarans','total_pengeluaran'));
    }

    public function show($nomor_pelanggan)
    {
        // Ambil data pelanggan
        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->firstOrFail();
    
        // Ambil riwayat pembayaran pelanggan
        $pembayarans = Pembayaran::where('nomor_pelanggan', $nomor_pelanggan)
            ->orderBy('tanggal_pembayaran', 'desc')
            ->get();
    
        // Ambil konfigurasi (tarif & denda)
        $konfigurasi = Konfigurasi::first();
        $tarif_per_kwh = json_decode($konfigurasi->tarif_per_kwh, true)[$pelanggan->kategori_tarif] ?? 1500;
        $denda_per_bulan = $konfigurasi->denda_bulanan ?? 5000;
    
        // Hitung total pemakaian listrik
        $total_pemakaian = max(0, $pelanggan->kwh_terakhir - $pelanggan->kwh_bulan_lalu);
        $total_tagihan = $total_pemakaian * $tarif_per_kwh;
    
        // Hitung total denda berdasarkan keterlambatan pembayaran
        $total_denda = 0;
        foreach ($pembayarans as $pembayaran) {
            if ($pembayaran->jumlah_dibayar < ($pembayaran->total_tagihan + $pembayaran->denda)) {
                $jatuh_tempo = Carbon::parse($pembayaran->tanggal_pembayaran)->addDays(30);
                $terlambat_bulan = max(0, Carbon::now()->diffInMonths($jatuh_tempo));
                $total_denda += $terlambat_bulan * $denda_per_bulan;
            }
        }
    
        // Total yang harus dibayar
        $total_yang_harus_dibayar = $total_tagihan + $total_denda;
    
        // Menentukan pembayaran terakhir
        $pembayaranTerakhir = $pembayarans->first(); // Ambil pembayaran pertama (terbaru)
    
        // Tentukan apakah sudah lunas atau belum
        $sudahTerbayar = $pembayaranTerakhir 
            ? $pembayaranTerakhir->jumlah_dibayar >= $total_yang_harus_dibayar
            : false;
    
        // Hitung total pembayaran yang telah dilakukan
        $totalPembayaran = $pembayarans->sum('jumlah_dibayar');
    
        return view('laporan.detail', compact(
            'pelanggan',
            'pembayarans',
            'total_pemakaian',
            'total_tagihan',
            'total_denda',
            'total_yang_harus_dibayar',
            'sudahTerbayar',  // Kirimkan status pembayaran
            'totalPembayaran' // Kirimkan total pembayaran
        ));
    }
    
    

    // EXPORT TO CSV
public function exportCsv(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->input('tanggal_selesai', now()->endOfMonth()->toDateString());
    
        // Ambil data pemasukan
        $pembayarans = Pembayaran::whereBetween('tanggal_pembayaran', [$tanggal_mulai, $tanggal_selesai])->get();
        
        // Ambil data pengeluaran
        $pengeluarans = Pengeluaran::whereBetween('tanggal', [$tanggal_mulai, $tanggal_selesai])->get();
    
        // Header CSV
        $csvData = [];
        $csvData[] = ["Laporan Keuangan", "Periode: $tanggal_mulai - $tanggal_selesai"];
        $csvData[] = [""];
        
        // Total Pemasukan
        $totalPemasukan = $pembayarans->sum('jumlah_dibayar');
        $csvData[] = ["Total Pemasukan", "Rp " . number_format($totalPemasukan, 0, ',', '.')];
    
        // Header tabel pemasukan
        $csvData[] = ["Tanggal", "Nomor Pelanggan", "Total Pemakaian (KWH)", "Total Tagihan", "Jumlah Dibayar"];
        foreach ($pembayarans as $pembayaran) {
            $csvData[] = [
                $pembayaran->tanggal_pembayaran,
                $pembayaran->nomor_pelanggan,
                number_format($pembayaran->total_pemakaian, 0, ',', '.'),
                "Rp " . number_format($pembayaran->total_tagihan, 0, ',', '.'),
                "Rp " . number_format($pembayaran->jumlah_dibayar, 0, ',', '.'),
            ];
        }
    
        // Tambahkan pemisah antar bagian CSV
        $csvData[] = [""];
        
        // Total Pengeluaran
        $totalPengeluaran = $pengeluarans->sum('jumlah');
        $csvData[] = ["Total Pengeluaran", "Rp " . number_format($totalPengeluaran, 0, ',', '.')];
    
        // Header tabel pengeluaran
        $csvData[] = ["Tanggal", "Jumlah", "Keterangan"];
        foreach ($pengeluarans as $pengeluaran) {
            $csvData[] = [
                $pengeluaran->tanggal,
                "Rp " . number_format($pengeluaran->jumlah, 0, ',', '.'),
                $pengeluaran->keterangan,
            ];
        }
    
        // Generate CSV content
        $filename = "Laporan_Keuangan_$tanggal_mulai\_$tanggal_selesai.csv";
        $handle = fopen('php://output', 'w');
        
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
    
        fclose($handle);
    
        return Response::make('', 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ]);
    }
    
    public function export(Request $request)
    {
        // Ambil tanggal dari filter
        $tanggal_mulai = $request->tanggal_mulai;
        $tanggal_selesai = $request->tanggal_selesai;

        // Ambil data pembayaran berdasarkan rentang tanggal
        $pembayarans = Pembayaran::whereBetween('tanggal_pembayaran', [$tanggal_mulai, $tanggal_selesai])
            ->orderBy('tanggal_pembayaran', 'desc')
            ->get();

        $filename = 'laporan_keuangan_' . date('Y-m-d') . '.csv';

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Tanggal', 'Nomor Pelanggan', 'Total Pemakaian', 'Total Tagihan', 'Jumlah Dibayar', 'Status']);

        foreach ($pembayarans as $pembayaran) {
            fputcsv($handle, [
                Carbon::parse($pembayaran->tanggal_pembayaran)->format('d-m-Y'),
                $pembayaran->nomor_pelanggan,
                number_format($pembayaran->total_pemakaian, 0, ',', '.') . ' KWH',
                'Rp ' . number_format($pembayaran->total_tagihan, 0, ',', '.'),
                'Rp ' . number_format($pembayaran->jumlah_dibayar, 0, ',', '.'),
                $pembayaran->jumlah_dibayar >= $pembayaran->total_tagihan ? 'Lunas' : 'Belum Lunas'
            ]);
        }

        fclose($handle);

        return Response::make('', 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
