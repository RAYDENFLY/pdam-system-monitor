<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('catatan_pemakaian', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pelanggan'); // Sama dengan tipe di pelanggans
            $table->integer('penggunaan_awal');
            $table->integer('penggunaan_akhir');
            $table->integer('jumlah_penggunaan');
            $table->string('periode_pemakaian');
            $table->date('batas_pembayaran');
            $table->timestamps();
        
            // Foreign key ke pelanggans.nomor_pelanggan
            $table->foreign('nomor_pelanggan')->references('nomor_pelanggan')->on('pelanggans')->onDelete('cascade');
        });        
    }

    public function down(): void {
        Schema::dropIfExists('catatan_pemakaian');
    }
};

