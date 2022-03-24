<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalRevColumnToSalesCommTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_commissions', function (Blueprint $table) {
            $table->integer('total_revenue')->default(0)->after('period_end');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_commissions', function (Blueprint $table) {
            $table->dropColumn('total_revenue');
        });
    }
}
