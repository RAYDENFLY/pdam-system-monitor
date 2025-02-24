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
            $table->decimal('biaya_abodemen', 10, 2)->default(0)->after('biaya_admin');
        });
    }
    
    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn('biaya_abodemen');
        });
    }
    
};
