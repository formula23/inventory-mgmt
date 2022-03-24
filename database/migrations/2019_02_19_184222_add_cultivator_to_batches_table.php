<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCultivatorToBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->integer('cultivator_id')->nullable()->index()->unsigned()->after('brand_id');
            $table->foreign('cultivator_id')->references('id')->on('users');

            $table->integer('testing_laboratory_id')->nullable()->index()->unsigned()->after('cultivator_id');
            $table->foreign('testing_laboratory_id')->references('id')->on('users');

            $table->date('tested_at')->nullable()->after('testing_status');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0'); // disable foreign key constraints

        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn('cultivator_id');
            $table->dropColumn('testing_laboratory_id');
            $table->dropColumn('tested_at');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); // disable foreign key constraints
    }
}
