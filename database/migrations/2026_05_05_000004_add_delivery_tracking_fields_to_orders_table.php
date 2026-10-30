<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'delivery_man_status')) {
                $table->string('delivery_man_status')->nullable()->index();
            }

            if (! Schema::hasColumn('orders', 'dispatcher_date')) {
                $table->timestamp('dispatcher_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'dispatcher_date')) {
                $table->dropColumn('dispatcher_date');
            }

            if (Schema::hasColumn('orders', 'delivery_man_status')) {
                $table->dropColumn('delivery_man_status');
            }
        });
    }
};
