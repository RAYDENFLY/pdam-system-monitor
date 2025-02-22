<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Buat tabel pelanggans dari awal
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->string('nomor_pelanggan')->primary();
            $table->string('nama');
            $table->text('alamat');
            $table->string('no_telepon');
            $table->date('tanggal_join')->nullable();
            $table->date('tanggal_pembayaran_terakhir')->nullable();
            $table->integer('kwh_terakhir')->default(0);
            $table->integer('kwh_bulan_lalu')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};
