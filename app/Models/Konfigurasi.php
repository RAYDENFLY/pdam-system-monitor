<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konfigurasi extends Model {
    use HasFactory;

    protected $fillable = [
        'denda_bulanan', 
        'tarif_per_kwh',
        'biaya_admin',
        'abodemen'
    ];

    protected $casts = [
        'tarif_per_kwh' => 'array',
        'biaya_admin' => 'decimal:2',
        'abodemen' => 'decimal:2'
    ];
}