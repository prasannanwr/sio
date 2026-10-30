<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vehicles') || Schema::hasColumn('vehicles', 'shipping_cost_id')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedBigInteger('shipping_cost_id')->nullable()->index()->after('delivery_man_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('vehicles') || ! Schema::hasColumn('vehicles', 'shipping_cost_id')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('shipping_cost_id');
        });
    }
};
