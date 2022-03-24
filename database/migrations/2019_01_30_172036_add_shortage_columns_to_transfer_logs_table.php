<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShortageColumnsToTransferLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_logs', function (Blueprint $table) {
            $table->integer('shortage')->default(0)->after('inventory_loss_grams');
            $table->float('shortage_grams')->default(0)->after('shortage');
//            $table->double('shortage_grams',8,2)->;
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
            $table->dropColumn('shortage');
            $table->dropColumn('shortage_grams');
        });
    }
}
