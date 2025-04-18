<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableOrderDetailsAllowNullBatchId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {

            \DB::statement("ALTER TABLE `".$table->getTable()."` CHANGE `batch_id` `batch_id` INT(10) UNSIGNED DEFAULT NULL");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `".$table->getTable()."` CHANGE `batch_id` `batch_id` INT(10) UNSIGNED DEFAULT NOT NULL");
        });
    }
}
