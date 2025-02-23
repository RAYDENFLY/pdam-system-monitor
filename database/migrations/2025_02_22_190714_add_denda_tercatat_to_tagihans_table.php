<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->decimal('denda_tercatat', 10, 2)->nullable()->after('harga_denda');
        });
    }

    public function down()
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropColumn('denda_tercatat');
        });
    }
};
