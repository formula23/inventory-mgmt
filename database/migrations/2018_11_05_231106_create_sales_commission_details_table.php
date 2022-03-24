<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesCommissionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_commission_details', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('sales_commission_id')->unsigned()->index();
            $table->foreign('sales_commission_id')->references('id')->on('sales_commissions');

            $table->integer('sales_rep_id')->unsigned()->index();
            $table->foreign('sales_rep_id')->references('id')->on('users');

            $table->integer('sale_order_id')->unsigned()->index();
            $table->foreign('sale_order_id')->references('id')->on('orders');

            $table->tinyInteger('is_bulk_order')->default(0);

            $table->float('rate', 5,4);
            $table->integer('amount');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_commission_details');
    }
}
