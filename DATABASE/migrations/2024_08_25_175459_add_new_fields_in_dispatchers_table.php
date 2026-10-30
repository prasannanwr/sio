<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFieldsInDispatchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispatchers', function (Blueprint $table) {
            $table->bigInteger('city_id')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('country_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dispatchers', function (Blueprint $table) {
            $table->dropColumn(['city_id','state_id','country_id']);
        });
    }
}
