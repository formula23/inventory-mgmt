<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('vendor_id')->nullable()->unsigned()->index();
            $table->foreign('vendor_id')->references('id')->on('users');

            $table->integer('customer_id')->nullable()->unsigned()->index();
            $table->foreign('customer_id')->references('id')->on('users');

            $table->date('txn_date');
                        
            $table->enum('type', ['purchase','sale','return'])->default('purchase')->index();
            $table->enum('status', ['open','closed'])->default('open')->indexed();
            
            $table->string('ref_number', 8)->unique()->index();
            
            $table->integer('total');
            $table->integer('balance');

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
        Schema::dropIfExists('orders');
    }
}
