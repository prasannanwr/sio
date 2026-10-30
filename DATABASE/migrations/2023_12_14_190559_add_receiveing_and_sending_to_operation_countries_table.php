<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceiveingAndSendingToOperationCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operation_countries', function (Blueprint $table) {
            $table->string('receiving')->default('no');
            $table->string('sending')->default('no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operation_countries', function (Blueprint $table) {
            $table->dropColumn('receiving');
            $table->dropColumn('sending');
        });
    }
}
