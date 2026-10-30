<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vehicles')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicles', 'rate_eur')) {
                $table->decimal('rate_eur', 10, 2)->nullable()->after('rate_per_km_per_kg');
            }

            if (! Schema::hasColumn('vehicles', 'rate_naira')) {
                $table->decimal('rate_naira', 12, 2)->nullable()->after('rate_eur');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('vehicles')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            foreach (['rate_eur', 'rate_naira'] as $column) {
                if (Schema::hasColumn('vehicles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
