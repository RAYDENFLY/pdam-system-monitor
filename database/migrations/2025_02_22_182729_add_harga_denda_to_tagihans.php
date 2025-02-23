<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->decimal('harga_denda', 10, 2)->default(5000)->change();
            $table->decimal('harga_kwh', 10, 2)->default(1500)->change();
        });
    }

    public function down()
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->decimal('harga_denda', 10, 2)->default(0)->change();
            $table->decimal('harga_kwh', 10, 2)->default(0)->change();
        });
    }
};
