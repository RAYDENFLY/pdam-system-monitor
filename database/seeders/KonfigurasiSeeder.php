<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Konfigurasi;

class KonfigurasiSeeder extends Seeder
{
    /**
     * Seed the konfigurasi table.
     *
     * @return void
     */
    public function run()
    {
        Konfigurasi::create([
            'denda_bulanan' => 5000,
            'tarif_per_kwh' => json_encode([
                'residential' => 1500,
                'commercial' => 2000,
                'industrial' => 2500,
            ]),
        ]);
    }
}
