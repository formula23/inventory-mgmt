<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotesPriceToTableVaultLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vault_logs', function (Blueprint $table) {
            $table->string('notes')->nullable()->after('strain_name');
            $table->integer('price')->nullable()->after('quantity');
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
            $table->dropColumn('price');
            $table->dropColumn('notes');
        });
    }
}
