<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCustTypeOnOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('orders', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `orders` CHANGE `customer_type` `customer_type` ENUM('distributor', 'retailer', 'retailer nonarms length', 'non-storefront retail')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {

            \DB::statement("ALTER TABLE `orders` CHANGE `customer_type` `customer_type` ENUM('distributor', 'retailer', 'non-storefront retail')");

        });
    }
}
