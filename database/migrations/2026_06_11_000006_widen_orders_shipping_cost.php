<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'shipping_cost')) {
            return;
        }

        // double(8,2) maxed at 999,999.99 — too small for NGN haulage amounts. Widen it.
        DB::statement('ALTER TABLE `orders` MODIFY `shipping_cost` DECIMAL(15,2) NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'shipping_cost')) {
            return;
        }

        DB::statement('ALTER TABLE `orders` MODIFY `shipping_cost` DOUBLE(8,2) NULL');
    }
};
