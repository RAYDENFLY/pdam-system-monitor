<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBiayaAdminAndAbodemenToKonfigurasis extends Migration
{
    public function up()
    {
        Schema::table('konfigurasis', function (Blueprint $table) {
            $table->decimal('biaya_admin', 10, 2)->default(2500);
            $table->decimal('abodemen', 10, 2)->default(3500);
        });
    }

    public function down()
    {
        Schema::table('konfigurasis', function (Blueprint $table) {
            $table->dropColumn(['biaya_admin', 'abodemen']);
        });
    }
}