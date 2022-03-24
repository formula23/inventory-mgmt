<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTermsDeliveryDateToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->integer('driver_id')->unsigned()->nullable()->index()->after('sales_rep_id');
            $table->foreign('driver_id')->references('id')->on('users');

            $table->integer('distributor_id')->unsigned()->nullable()->index()->after('driver_id');
            $table->foreign('distributor_id')->references('id')->on('users');

            $table->tinyInteger('terms')->nullable()->after('txn_date');
            $table->dateTime('delivered_at')->nullable()->after('due_date');
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
            $table->dropColumn('driver_id');
            $table->dropColumn('distributor_id');
            $table->dropColumn('terms');
            $table->dropColumn('delivered_at');
        });
    }
}
