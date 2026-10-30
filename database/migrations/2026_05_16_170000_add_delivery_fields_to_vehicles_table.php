<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryFieldsToVehiclesTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('vehicles')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicles', 'origin_location')) {
                $table->string('origin_location')->nullable()->after('courier_id');
            }

            if (! Schema::hasColumn('vehicles', 'destination_location')) {
                $table->string('destination_location')->nullable()->after('origin_location');
            }

            if (! Schema::hasColumn('vehicles', 'weight_range')) {
                $table->string('weight_range')->nullable()->after('destination_location');
            }

            if (! Schema::hasColumn('vehicles', 'rate_per_km_per_kg')) {
                $table->decimal('rate_per_km_per_kg', 10, 2)->nullable()->after('weight_range');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('vehicles')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            foreach (['rate_per_km_per_kg', 'weight_range', 'destination_location', 'origin_location'] as $column) {
                if (Schema::hasColumn('vehicles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
