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

    public function harian(Request $request)
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


        return view('laporan.harian', compact('pembayarans', 'total_pemasukan', 'tanggal_mulai', 'tanggal_selesai','pengeluarans','total_pengeluaran'));
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
    
        // Ambil biaya admin dan abonemen dari pembayaran terakhir (jika ada)
        $pembayaranTerakhir = $pembayarans->first(); // Pembayaran terbaru
        $biaya_admin = $pembayaranTerakhir ? $pembayaranTerakhir->biaya_admin : 0;
        $biaya_abodemen = $pembayaranTerakhir ? $pembayaranTerakhir->biaya_abodemen : 0;
    
        // Hitung total denda berdasarkan keterlambatan pembayaran
        $total_denda = 0;
        foreach ($pembayarans as $pembayaran) {
            if ($pembayaran->jumlah_dibayar < ($pembayaran->total_tagihan + $pembayaran->denda)) {
                $jatuh_tempo = Carbon::parse($pembayaran->tanggal_pembayaran)->addDays(30);
                $terlambat_bulan = max(0, Carbon::now()->diffInMonths($jatuh_tempo));
                $total_denda += $terlambat_bulan * $denda_per_bulan;
            }
        }
    
        // Total yang harus dibayar (termasuk biaya admin dan abonemen)
        $total_yang_harus_dibayar = $total_tagihan + $total_denda + $biaya_admin + $biaya_abodemen;
    
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
            'biaya_admin', // Sekarang dari tabel `pembayarans`
            'biaya_abodemen', // Sekarang dari tabel `pembayarans`
            'total_yang_harus_dibayar',
            'sudahTerbayar',
            'totalPembayaran'
        ));
    }
    
    public function keseluruhan(Request $request)
    {
        // Ambil tanggal dari request atau gunakan default (bulan ini)
        $tanggal_mulai = $request->query('tanggal_mulai', now()->startOfMonth()->toDateString());
        $tanggal_selesai = $request->query('tanggal_selesai', now()->endOfMonth()->toDateString());
    
        // Ambil data pemasukan dari Pembayaran
        $pembayarans = Pembayaran::whereBetween('tanggal_pembayaran', [$tanggal_mulai, $tanggal_selesai])
            ->orderBy('tanggal_pembayaran', 'asc')
            ->get();
    
        // Ambil data pengeluaran dari Pengeluaran
        $pengeluarans = Pengeluaran::whereBetween('tanggal', [$tanggal_mulai, $tanggal_selesai])
            ->orderBy('tanggal', 'asc')
            ->get();
    
        // Hitung total pemasukan dan pengeluaran
        $total_pemasukan = $pembayarans->sum('jumlah_dibayar');
        $total_pengeluaran = $pengeluarans->sum('jumlah');
    
        // Buat array laporan untuk ditampilkan di view
        $laporanKeseluruhan = [];
    
        // Gabungkan data pemasukan dan pengeluaran dalam satu array
        foreach ($pembayarans as $pembayaran) {
            $laporanKeseluruhan[] = [
                'tanggal' => $pembayaran->tanggal_pembayaran,
                'keterangan' => 'Pemasukan dari ' . $pembayaran->nomor_pelanggan,
                'pemasukan' => $pembayaran->jumlah_dibayar,
                'pengeluaran' => 0, // Tidak ada pengeluaran di data ini
            ];
        }
    
        foreach ($pengeluarans as $pengeluaran) {
            $laporanKeseluruhan[] = [
                'tanggal' => $pengeluaran->tanggal,
                'keterangan' => 'Pengeluaran: ' . $pengeluaran->keterangan,
                'pemasukan' => 0, // Tidak ada pemasukan di data ini
                'pengeluaran' => $pengeluaran->jumlah,
            ];
        }
    
        // Urutkan laporan berdasarkan tanggal
        usort($laporanKeseluruhan, function ($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });
    
        return view('laporan.keseluruhan', compact('laporanKeseluruhan', 'tanggal_mulai', 'tanggal_selesai', 'total_pemasukan', 'total_pengeluaran'));
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
