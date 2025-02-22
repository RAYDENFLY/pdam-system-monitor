<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pelanggan',
        'total_pemakaian',
        'total_tagihan',
        'jumlah_dibayar',
        'tanggal_pembayaran',
        'denda',
    ];

    public function pelanggan()
{
    return $this->belongsTo(Pelanggan::class, 'nomor_pelanggan', 'nomor_pelanggan');
}
}
