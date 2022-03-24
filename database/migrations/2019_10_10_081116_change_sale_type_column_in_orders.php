<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSaleTypeColumnInOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `orders` MODIFY COLUMN sale_type VARCHAR(50) DEFAULT 'packaged'");
        \DB::statement("UPDATE `orders` SET sale_type = 'packaged' WHERE sale_type='retail'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
