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
        return 0;
    }

    $pembayaranTerakhir = Pembayaran::where('nomor_pelanggan', $nomor_pelanggan)
        ->whereNotNull('tanggal_pembayaran')
        ->latest()
        ->first();

    $konfigurasi = Konfigurasi::first();
    $denda_bulanan = $konfigurasi->denda_bulanan ?? 5000; // Default 5000

    // Jika belum ada pembayaran, gunakan tanggal join
    $tanggal_pembayaran_terakhir = $pembayaranTerakhir 
        ? Carbon::parse($pembayaranTerakhir->tanggal_pembayaran) 
        : Carbon::parse($pelanggan->tanggal_join);

    // Tentukan tanggal jatuh tempo bulan berikutnya
    $tanggal_jatuh_tempo = Carbon::create(
        $tanggal_pembayaran_terakhir->year,
        $tanggal_pembayaran_terakhir->month,
        20
    )->addMonth();

    // Jika sekarang belum melewati tanggal jatuh tempo, tidak ada denda
    if (Carbon::now()->lessThan($tanggal_jatuh_tempo)) {
        return 0;
    }

    // Hitung berapa bulan telat dari jatuh tempo sampai sekarang
    $bulan_telat = Carbon::now()->startOfDay()->diffInMonths($tanggal_jatuh_tempo);

    // Hitung total denda
    $total_denda = $bulan_telat * $denda_bulanan;

    // Pastikan tetap dalam kelipatan Rp 5000
    $total_denda = ceil($total_denda / 5000) * 5000;

    // Debugging log
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
public static function hitungTotalPembayaran($jumlah_tagihan, $denda) {
    // Tambahkan denda ke total tagihan
    return $jumlah_tagihan + $denda;
}
    

public function isLunas()
{
    return $this->jumlah_dibayar >= ($this->total_tagihan + $this->denda);
}
    public function scopeBelumLunas($query)
    {
        return $query->where('jumlah_dibayar', '<', 'total_tagihan');
    }
    
}
