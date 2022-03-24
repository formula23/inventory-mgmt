<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('purchase_order_id')->nullable()->unsigned()->index();
            $table->foreign('purchase_order_id')->references('id')->on('orders');

            $table->integer('category_id')->unsigned()->index();
            $table->foreign('category_id')->references('id')->on('categories');

            $table->enum('status', ['received','lab','inventory','sold','destroyed','failed'])->default('received');

            $table->string('name');
            $table->text('description')->nullable();
            $table->json('character')->nullable();
            $table->text('sales_notes')->nullable();

            $table->string('type')->nullable();
            $table->string('ref_number', 8)->unique()->index();

            $table->float('units_purchased')->unsigned()->default(0);
            $table->float('inventory')->unsigned()->default(0);
            $table->float('transit')->unsigned()->default(0);
            $table->float('sold')->unsigned()->default(0);

            $table->enum('uom', ['lb','g','unit'])->default('g');

            $table->integer('unit_price');
            $table->integer('subtotal_price')->nullable();
            $table->integer('suggested_unit_sale_price')->unsigned()->nullable();

            $table->integer('min_flex')->default(0);
            $table->integer('max_flex')->default(0);

            $table->string('bin_number')->nullable();

            $table->float('thc',5)->nullable();
            $table->float('cbd',5)->nullable();
            $table->float('cbn',5)->nullable();
            $table->tinyInteger('is_medical')->unsigned()->default(1);
            $table->date('cultivation_date')->nullable();

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
        Schema::dropIfExists('batches');
    }
}
