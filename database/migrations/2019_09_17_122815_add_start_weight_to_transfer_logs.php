<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartWeightToTransferLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_logs', function (Blueprint $table) {
            $table->double('start_wt_grams', 10, 4)->after('quantity_transferred')->default(0);
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
            $table->dropColumn('start_wt_grams');
        });
    }
}
