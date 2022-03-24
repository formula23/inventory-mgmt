<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBrokerIdToVaultLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vault_logs', function (Blueprint $table) {
            $table->integer('broker_id')->nullable()->unsigned()->index()->after('user_id');
            $table->foreign('broker_id')->references('id')->on('users');
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
            $table->dropForeign(['broker_id']);
            $table->dropIndex(['broker_id']);
            $table->dropColumn('broker_id');
        });
    }
}
