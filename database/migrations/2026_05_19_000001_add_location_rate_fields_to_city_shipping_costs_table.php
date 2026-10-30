<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationRateFieldsToCityShippingCostsTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('city_shipping_costs')) {
            return;
        }

        Schema::table('city_shipping_costs', function (Blueprint $table) {
            if (! Schema::hasColumn('city_shipping_costs', 'origin_city_id')) {
                $table->unsignedBigInteger('origin_city_id')->nullable()->index()->after('id');
            }

            if (! Schema::hasColumn('city_shipping_costs', 'destination_city_id')) {
                $table->unsignedBigInteger('destination_city_id')->nullable()->index()->after('origin_city_id');
            }

            if (! Schema::hasColumn('city_shipping_costs', 'day')) {
                $table->string('day')->nullable()->after('percentage');
            }

            if (! Schema::hasColumn('city_shipping_costs', 'weight')) {
                $table->decimal('weight', 12, 2)->nullable()->after('day');
            }

            if (! Schema::hasColumn('city_shipping_costs', 'weight_range')) {
                $table->string('weight_range')->nullable()->after('weight');
            }

            if (! Schema::hasColumn('city_shipping_costs', 'rate_per_km_per_kg')) {
                $table->decimal('rate_per_km_per_kg', 12, 2)->nullable()->after('cost');
            }

            if (! Schema::hasColumn('city_shipping_costs', 'distance_km')) {
                $table->decimal('distance_km', 12, 2)->nullable()->after('rate_per_km_per_kg');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('city_shipping_costs')) {
            return;
        }

        Schema::table('city_shipping_costs', function (Blueprint $table) {
            foreach (['origin_city_id', 'destination_city_id', 'weight_range', 'rate_per_km_per_kg', 'distance_km'] as $column) {
                if (Schema::hasColumn('city_shipping_costs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
