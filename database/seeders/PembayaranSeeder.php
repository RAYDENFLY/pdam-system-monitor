<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelanggan;
use Carbon\Carbon;

class PelangganSeeder extends Seeder
{
    public function run()
    {
        Pelanggan::insert([
            [
                'nomor_pelanggan' => '100001',
                'nama' => 'Budi Santoso',
                'kategori_tarif' => 'R1',
                'kwh_bulan_lalu' => 1000,
                'kwh_terakhir' => 1120,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nomor_pelanggan' => '100002',
                'nama' => 'Siti Aminah',
                'kategori_tarif' => 'R2',
                'kwh_bulan_lalu' => 1200,
                'kwh_terakhir' => 1350,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nomor_pelanggan' => '100003',
                'nama' => 'Ahmad Fauzi',
                'kategori_tarif' => 'R1',
                'kwh_bulan_lalu' => 800,
                'kwh_terakhir' => 1000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nomor_pelanggan' => '100004',
                'nama' => 'Dewi Lestari',
                'kategori_tarif' => 'R3',
                'kwh_bulan_lalu' => 1500,
                'kwh_terakhir' => 1680,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
