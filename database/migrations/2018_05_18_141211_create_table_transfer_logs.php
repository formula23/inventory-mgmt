<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTransferLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_logs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->nullable()->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('batch_id')->nullable()->unsigned()->index();
            $table->foreign('batch_id')->references('id')->on('batches');

            $table->integer('quantity_transferred');

            $table->integer('inventory_loss')->default(0);

            $table->string('packer_name')->nullable();
            $table->date('packed_date');

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
        Schema::dropIfExists('transfer_logs');
    }
}
