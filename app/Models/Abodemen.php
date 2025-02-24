<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abodemen extends Model
{
    use HasFactory;

    protected $table = 'abodemens'; // Sesuaikan dengan nama tabel di database
    protected $primaryKey = 'nomor_pelanggan'; // Jika primary key bukan "id"
    public $timestamps = false; // Jika tidak ada kolom created_at & updated_at

    protected $fillable = [
        'nomor_pelanggan',
        'abodemen',
    ];
}
