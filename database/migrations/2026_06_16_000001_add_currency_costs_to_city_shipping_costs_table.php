<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('city_shipping_costs')) {
            return;
        }

        Schema::table('city_shipping_costs', function (Blueprint $table) {
            if (! Schema::hasColumn('city_shipping_costs', 'cost_eur')) {
                $table->decimal('cost_eur', 15, 2)->nullable()->after('cost');
            }

            if (! Schema::hasColumn('city_shipping_costs', 'cost_naira')) {
                $table->decimal('cost_naira', 15, 2)->nullable()->after('cost_eur');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('city_shipping_costs')) {
            return;
        }

        Schema::table('city_shipping_costs', function (Blueprint $table) {
            foreach (['cost_eur', 'cost_naira'] as $column) {
                if (Schema::hasColumn('city_shipping_costs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
