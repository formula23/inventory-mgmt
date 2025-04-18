<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeDeleteToLicenseTyperUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('license_type_user', function (Blueprint $table) {
            $table->dropForeign('license_type_user_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('license_type_user', function (Blueprint $table) {
            $table->dropForeign('license_type_user_user_id_foreign');
            $table->foreign('user_id')->references('id')->on('users')->change();
        });
    }
}
