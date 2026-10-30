<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryFieldsToCouriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('couriers', function (Blueprint $table) {

            $table->string('origin_location')->nullable()->after('vehicle_type');

            $table->string('destination_location')->nullable()->after('origin_location');

            $table->string('weight_range')->nullable()->after('destination_location');

            $table->decimal('rate_per_km_per_kg', 10, 2)
                ->nullable()
                ->after('weight_range');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('couriers', function (Blueprint $table) {

            $table->dropColumn([
                'origin_location',
                'destination_location',
                'weight_range',
                'rate_per_km_per_kg',
            ]);

        });
    }
}
