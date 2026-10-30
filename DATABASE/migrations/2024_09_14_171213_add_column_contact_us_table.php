<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnContactUsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_us', function (Blueprint $table) {
            $table->bigInteger('ship_from_country_id')->nullable();
            $table->bigInteger('ship_from_city_id')->nullable();
            $table->bigInteger('ship_to_country_id')->nullable();
            $table->bigInteger('ship_to_city_id')->nullable();
            $table->string('ship_from_State_name')->nullable();
            $table->string('ship_to_state_name')->nullable();
            $table->float('total_weight')->nullable();
            $table->float('shipping_cost')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_us', function (Blueprint $table) {
            //
        });
    }
}
