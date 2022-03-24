<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderDetailIdToVaultLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vault_logs', function (Blueprint $table) {
            $table->integer('order_detail_id')->nullable()->unsigned()->index()->after('batch_id');
            $table->foreign('order_detail_id')->references('id')->on('order_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vault_logs', function (Blueprint $table) {
            $table->dropForeign(['order_detail_id']);
            $table->dropIndex(['order_detail_id']);
            $table->dropColumn('order_detail_id');
        });
    }
}
