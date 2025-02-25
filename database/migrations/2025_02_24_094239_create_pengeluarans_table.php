<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal'); // Tanggal pengeluaran
            $table->decimal('jumlah', 15, 2); // Jumlah uang yang dikeluarkan
            $table->string('keterangan')->nullable(); // Keterangan opsional
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengeluarans');
    }
};

