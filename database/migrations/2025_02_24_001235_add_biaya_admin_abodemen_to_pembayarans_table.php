<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->integer('biaya_admin')->default(2500);
            $table->integer('abodemen')->default(5000);
        });
    }
    
    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['biaya_admin', 'abodemen']);
        });
    }
    
};
