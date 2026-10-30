<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickupCostToShippingRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipping_rates', function (Blueprint $table) {
            $table->float('delivery_cost_per_km')->after('height')->nullable()->default(0);
            $table->float('pickup_cost_per_km')->after('height')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipping_rates', function (Blueprint $table) {
            //
        });
    }
}
