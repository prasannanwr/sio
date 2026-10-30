<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCargoDocumentsToOrdersTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'invoice_document')) {
                $table->string('invoice_document')->nullable();
            }

            if (! Schema::hasColumn('orders', 'packing_list_document')) {
                $table->string('packing_list_document')->nullable();
            }

            if (! Schema::hasColumn('orders', 'id_document')) {
                $table->string('id_document')->nullable();
            }

            if (! Schema::hasColumn('orders', 'permit_document')) {
                $table->string('permit_document')->nullable();
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            foreach (['permit_document', 'id_document', 'packing_list_document'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
