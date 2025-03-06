<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanPemakaian extends Model
{
    use HasFactory;

    protected $table = 'catatan_pemakaian'; // Tentukan nama tabel yang benar

    protected $fillable = [
        'nomor_pelanggan',
        'penggunaan_awal',
        'penggunaan_akhir',
        'jumlah_penggunaan',
        'periode_pemakaian',
    ];
}
