<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bank_details')) {
            return;
        }

        Schema::table('bank_details', function (Blueprint $table) {
            if (! Schema::hasColumn('bank_details', 'acount_number')) {
                $table->string('acount_number')->nullable()->after('iban');
            }

            if (! Schema::hasColumn('bank_details', 'routing_number')) {
                $table->string('routing_number')->nullable()->after('acount_number');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bank_details')) {
            return;
        }

        Schema::table('bank_details', function (Blueprint $table) {
            if (Schema::hasColumn('bank_details', 'routing_number')) {
                $table->dropColumn('routing_number');
            }

            if (Schema::hasColumn('bank_details', 'acount_number')) {
                $table->dropColumn('acount_number');
            }
        });
    }
};
