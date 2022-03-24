<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUomColOnBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `batches` CHANGE `uom` `uom` ENUM('lb','g','unit','1/8 oz','1/4 oz','1/2 oz','1oz') not null default 'g'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE `batches` CHANGE `uom` `uom` ENUM('lb','g','unit') not null default 'g'");
    }
}
