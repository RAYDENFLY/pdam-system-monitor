<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('konfigurasis', function (Blueprint $table) {
            $table->id();
            $table->integer('denda_bulanan')->default(5000); // Default denda
            $table->json('tarif_per_kwh')->nullable(); // Tarif dalam bentuk JSON
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('konfigurasis');
    }
};
