<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->renameColumn('abodemen', 'biaya_langganan');
        });
    }

    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->renameColumn('biaya_langganan', 'abodemen');
        });
    }
};
