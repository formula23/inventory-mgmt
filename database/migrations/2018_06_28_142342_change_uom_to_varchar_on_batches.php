<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUomToVarcharOnBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batches', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `batches` CHANGE `uom` `uom` VARCHAR(50)  CHARACTER SET utf8mb4  COLLATE utf8mb4_unicode_ci  NOT NULL  DEFAULT 'g'");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batches', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `batches` CHANGE `uom` `uom` ENUM('lb','g','unit','1/8 oz','1/4 oz','1/2 oz','1oz') not null default 'g'");
        });
    }
}
