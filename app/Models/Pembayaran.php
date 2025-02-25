<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pembayaran extends Model {
    use HasFactory;

    protected $table = 'pembayarans'; // Sesuaikan dengan nama tabel di database
    protected $fillable = [
        'nomor_pelanggan',
        'total_pemakaian',
        'total_tagihan',
        'jumlah_dibayar',
        'tanggal_pembayaran',
        'denda',
        'biaya_admin',
        'biaya_abodemen',
    ];

    // Tambahkan relasi ke model Pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'nomor_pelanggan', 'nomor_pelanggan');
    }

    // Function untuk menghitung denda
    public static function hitungDenda($nomor_pelanggan) {
        $pelanggan = Pelanggan::where('nomor_pelanggan', $nomor_pelanggan)->first();
        if (!$pelanggan) {
            return 0; // Jika pelanggan tidak ditemukan, tidak ada denda
        }

        $konfigurasi = Konfigurasi::first();
        $denda_bulanan = optional($konfigurasi)->denda_bulanan ?? 5000; // Default ke 5000 jika kosong

        // Ambil pembayaran terakhir yang sudah dibayar
        $pembayaranTerakhir = Pembayaran::where('nomor_pelanggan', $nomor_pelanggan)
            ->whereNotNull('tanggal_pembayaran')
            ->latest()
            ->first();

        // Jika tidak ada pembayaran terakhir, gunakan tanggal join pelanggan
        $tanggal_pembayaran_terakhir = $pembayaranTerakhir
            ? Carbon::parse($pembayaranTerakhir->tanggal_pembayaran)
            : Carbon::parse($pelanggan->tanggal_join);

        // Cek apakah pembayaran terakhir sudah lunas
        if ($pembayaranTerakhir && $pembayaranTerakhir->isLunas()) {
            \Log::info("Pelanggan {$nomor_pelanggan} sudah lunas, tidak ada denda.");
            return 0;
        }

        // Tentukan tanggal jatuh tempo pembayaran berikutnya (tanggal 20 bulan berikutnya)
        $tanggal_jatuh_tempo = Carbon::create(
            $tanggal_pembayaran_terakhir->year,
            $tanggal_pembayaran_terakhir->month,
            20
        )->addMonth();

        // Jika sekarang belum melewati tanggal jatuh tempo, tidak ada denda
        if (Carbon::now()->lessThan($tanggal_jatuh_tempo)) {
            \Log::info("Pelanggan {$nomor_pelanggan} masih dalam periode pembayaran, tidak ada denda.");
            return 0;
        }

        // Hitung jumlah bulan keterlambatan
        $bulan_telat = Carbon::now()->startOfDay()->diffInMonths($tanggal_jatuh_tempo);

        // Hitung total denda berdasarkan bulan keterlambatan
        $total_denda = $bulan_telat * $denda_bulanan;

        // Pastikan denda dalam kelipatan 5000
        $total_denda = ceil($total_denda / 5000) * 5000;

        // Logging untuk debugging
        \Log::info("===== DEBUG HITUNG DENDA =====");
        \Log::info("Nomor Pelanggan: " . $nomor_pelanggan);
        \Log::info("Tanggal Pembayaran Terakhir: " . $tanggal_pembayaran_terakhir->format('Y-m-d'));
        \Log::info("Tanggal Jatuh Tempo: " . $tanggal_jatuh_tempo->format('Y-m-d'));
        \Log::info("Sekarang: " . Carbon::now()->format('Y-m-d'));
        \Log::info("Bulan Telat: " . $bulan_telat);
        \Log::info("Total Denda: " . $total_denda);

        return $total_denda;
    }

    // Function untuk menghitung total pembayaran
    public static function hitungTotalPembayaran($jumlah_tagihan, $denda, $biaya_admin = 2500, $biaya_abodemen = 0) {
        return $jumlah_tagihan + $denda + $biaya_admin + $biaya_abodemen;
    }

    // Cek apakah pembayaran sudah lunas
    public function isLunas()
    {
        return $this->jumlah_dibayar >= $this->hitungTotalPembayaran(
            $this->total_tagihan,
            0, // Denda tidak diperhitungkan di sini, karena jika sudah lunas, denda tidak ada
            $this->biaya_admin,
            $this->biaya_abodemen
        );
    }

    // Scope untuk mengambil pembayaran yang belum lunas
    public function scopeBelumLunas($query)
    {
        return $query->where('jumlah_dibayar', '<', 'total_tagihan');
    }
}
