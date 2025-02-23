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

    // Tambahkan relasi ke model Konfigurasi
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
        $denda_bulanan = $konfigurasi->denda_bulanan ?? 5000; // Pastikan nilainya minimal 5000
    
        $tanggal_pembayaran_terakhir = $pembayaranTerakhir 
            ? Carbon::parse($pembayaranTerakhir->tanggal_pembayaran) 
            : Carbon::parse($pelanggan->tanggal_join);
    
        $bulan_telat = max(1, $tanggal_pembayaran_terakhir->diffInMonths(Carbon::now())); // Pastikan minimal 1 bulan
    
        return $bulan_telat * $denda_bulanan;
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
