<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCityShippingCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::dropIfExists('city_shipping_costs');
        Schema::create('city_shipping_costs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('city_id')->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->float('percentage')->nullable();
            $table->float('cost')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_shipping_costs');
    }
}
