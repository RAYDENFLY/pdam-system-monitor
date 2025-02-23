<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;  // Ensure you import the Contact model here

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed the Contact table
        Contact::create([
            'lokasi' => 'Pasar Cipanas, Kecamatan Cipanas, Kabupaten Cianjur, Provinsi Jawa Barat, 43253',
            'email' => 'listrikpasarcipanas@listrik.com',
            'no_hp' => '0812-3456-7890',
        ]);
    }
}
