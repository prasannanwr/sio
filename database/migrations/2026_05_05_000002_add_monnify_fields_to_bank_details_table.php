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
            if (! Schema::hasColumn('bank_details', 'account_name')) {
                $table->string('account_name')->nullable()->after('bank_name');
            }

            if (! Schema::hasColumn('bank_details', 'bank_code')) {
                $table->string('bank_code')->nullable()->after('bank_name');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bank_details')) {
            return;
        }

        Schema::table('bank_details', function (Blueprint $table) {
            if (Schema::hasColumn('bank_details', 'account_name')) {
                $table->dropColumn('account_name');
            }

            if (Schema::hasColumn('bank_details', 'bank_code')) {
                $table->dropColumn('bank_code');
            }
        });
    }
};
