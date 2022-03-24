<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRndThcResultsToBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->double('thc_rnd', 5, 2)->nullable()->after('thc');
            $table->double('cbd_rnd', 5, 2)->nullable()->after('cbd');
            $table->double('cbn_rnd', 5, 2)->nullable()->after('cbn');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn('thc_rnd');
            $table->dropColumn('cbd_rnd');
            $table->dropColumn('cbn_rnd');
        });
    }
}
