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
            if (! Schema::hasColumn('transactions', 'transaction_type')) {
                $table->string('transaction_type')->nullable()->index();
            }

            if (! Schema::hasColumn('transactions', 'payment_provider')) {
                $table->string('payment_provider')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('transactions')) {
            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'payment_provider')) {
                $table->dropColumn('payment_provider');
            }

            if (Schema::hasColumn('transactions', 'transaction_type')) {
                $table->dropColumn('transaction_type');
            }
        });
    }
};
