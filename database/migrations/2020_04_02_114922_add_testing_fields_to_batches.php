<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTestingFieldsToBatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->tinyInteger('coa_batch')->default(0)->unsigned()->after('packaged_date');
            $table->tinyInteger('coa_sample')->default(0)->unsigned()->after('coa_batch');
            $table->string('rnd_link')->nullable()->after('coa_sample');

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
            $table->dropColumn('rnd_link');
            $table->dropColumn('coa_sample');
            $table->dropColumn('coa_batch');
        });
    }
}
