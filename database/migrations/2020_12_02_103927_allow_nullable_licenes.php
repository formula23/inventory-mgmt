<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowNullableLicenes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->string('legal_business_name')->nullable()->change();
            $table->string('premise_address')->nullable()->change();
            $table->string('premise_city')->nullable()->change();
            $table->string('premise_zip')->nullable()->change();

            $table->date('valid')->nullable()->change();
            $table->date('expires')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->string('legal_business_name')->nullable(false)->change();
            $table->string('premise_address')->nullable(false)->change();
            $table->string('premise_city')->nullable(false)->change();
            $table->string('premise_zip')->nullable(false)->change();

            $table->date('valid')->nullable(false)->change();
            $table->date('expires')->nullable(false)->change();
        });
    }
}
