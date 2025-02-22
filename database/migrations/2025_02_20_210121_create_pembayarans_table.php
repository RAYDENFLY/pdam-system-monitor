<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pelanggan'); // Hubungkan ke pelanggan
            $table->integer('total_pemakaian'); // Pemakaian KWH
            $table->integer('total_tagihan'); // Tagihan dalam Rupiah
            $table->integer('jumlah_dibayar'); // Jumlah yang dibayarkan
            $table->date('tanggal_pembayaran'); // Tanggal transaksi
            $table->timestamps();

            // Tambahkan foreign key ke tabel pelanggan
            $table->foreign('nomor_pelanggan')->references('nomor_pelanggan')->on('pelanggans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
