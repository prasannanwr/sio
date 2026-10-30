<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transactions')) {
            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'order_id')) {
                $table->unsignedBigInteger('order_id')->nullable()->index()->after('user_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('transactions')) {
            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'order_id')) {
                $table->dropColumn('order_id');
            }
        });
    }
};
