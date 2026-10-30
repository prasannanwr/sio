<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        // The previous ENUM only allowed 6 values, but the app's OrderStatus has more
        // (in_delivery_man, new_order, processing, Accepted By Agent, Customer Received).
        // Writing an out-of-enum value silently stored '' — widen to a string.
        DB::statement("ALTER TABLE `orders` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'unpaid'");

        // Repair rows that were blanked by an out-of-enum write.
        DB::table('orders')->where('status', '')->update(['status' => 'unpaid']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('unpaid','placed','assigned','in_transit','delivered','cancelled') NOT NULL DEFAULT 'unpaid'");
    }
};
