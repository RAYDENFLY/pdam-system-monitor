<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konfigurasi extends Model {
    use HasFactory;

    protected $fillable = [
        'denda_bulanan', 
        'tarif_per_kwh',
    ];

    protected $casts = [
        'tarif_per_kwh' => 'array',
    ];
}