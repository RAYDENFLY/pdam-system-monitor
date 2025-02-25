<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $primaryKey = 'nomor_pelanggan'; // ✅ Pakai nomor pelanggan sebagai Primary Key
    public $incrementing = false; // ✅ Matikan auto-increment
    protected $keyType = 'string'; // ✅ Set primary key jadi string

    protected $fillable = [
        'nomor_pelanggan',
        'nama',
        'alamat',
        'no_telepon',
        'tanggal_join', 
        'tanggal_pembayaran_terakhir',
        'kwh_terakhir',
        'kwh_bulan_lalu',
        'kategori_tarif',
        'block_rumah',
    ];

    // ✅ Tambahkan relasi ke Pembayaran
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'nomor_pelanggan', 'nomor_pelanggan');
    }

    // Add this to your Pelanggan model
    public function getTarifDayaAttribute()
    {
        $konfigurasi = Konfigurasi::first();
        $tarif = $konfigurasi->tarif_per_kwh[$this->kategori_tarif] ?? 1500;
        return $this->kategori_tarif . ' / ' . $tarif . ' per kWh';
    }
}
