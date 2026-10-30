<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFreightModeToOrdersTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('orders') || Schema::hasColumn('orders', 'freight_mode')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $column = $table->string('freight_mode', 20)->nullable();

            if (Schema::hasColumn('orders', 'provider')) {
                $column->after('provider');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('orders') || ! Schema::hasColumn('orders', 'freight_mode')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('freight_mode');
        });
    }
}
