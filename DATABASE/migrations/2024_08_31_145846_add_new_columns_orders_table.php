<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
                 
                 $table->bigInteger('customer_id')->nullable();
                 $table->bigInteger('walk_in_customer_id')->nullable();
                 $table->bigInteger('delivery_customer_id')->nullable();
                 $table->bigInteger('courier_id')->nullable();
                 $table->bigInteger('dispatcher_id')->nullable();
                 $table->bigInteger('batch_id')->nullable();
                 $table->bigInteger('shipping_rate_id')->nullable();
                 $table->bigInteger('pickup_location_id')->nullable();
                 $table->bigInteger('delivery_location_id')->nullable();
                 $table->bigInteger('current_location_id')->nullable();
                 
                 $table->bigInteger('current_location_country_id')->nullable();
                 $table->bigInteger('current_location_city_id')->nullable();
                 $table->bigInteger('pickup_location_country_id')->nullable();
                 $table->bigInteger('pickup_location_city_id')->nullable();
                 $table->bigInteger('delivery_location_country_id')->nullable();
                 $table->bigInteger('delivery_location_city_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
