<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgencyColsToDispatchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dispatchers', function (Blueprint $table) {
            $table->string('agency_type')->default('person');
            $table->string('business_name')->nullable();
            $table->string('tax_id_code')->nullable();
            $table->string('vat_no')->nullable();
            $table->string('pec')->nullable();
            $table->string('sdi')->nullable();
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
            //
        });
    }
}
