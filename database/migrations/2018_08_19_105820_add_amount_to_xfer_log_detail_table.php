<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAmountToXferLogDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_log_details', function (Blueprint $table) {
            $table->string('action')->after('batch_id');
            $table->float('units')->unsigned()->default(0)->after('action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_log_details', function (Blueprint $table) {
            $table->dropColumn('action');
            $table->dropColumn('units');
        });
    }
}
