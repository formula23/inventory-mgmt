<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeChangeQtyTypeToTransferLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_logs', function (Blueprint $table) {
            $table->decimal('quantity_transferred', 8, 2)->unsigned()->change();
            $table->string('type', 20)->default('Pre-Pack')->after('packer_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_logs', function (Blueprint $table) {
            $table->integer('quantity_transferred')->change();
            $table->dropColumn('type');
        });
    }
}
