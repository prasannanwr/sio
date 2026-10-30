<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFreightDocumentsToCityShippingCostsTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('city_shipping_costs')) {
            return;
        }

        Schema::table('city_shipping_costs', function (Blueprint $table) {
            if (! Schema::hasColumn('city_shipping_costs', 'freight_mode')) {
                $table->string('freight_mode', 20)->nullable();
            }

            if (! Schema::hasColumn('city_shipping_costs', 'invoice_document')) {
                $table->string('invoice_document')->nullable();
            }

            if (! Schema::hasColumn('city_shipping_costs', 'packing_list_document')) {
                $table->string('packing_list_document')->nullable();
            }

            if (! Schema::hasColumn('city_shipping_costs', 'id_document')) {
                $table->string('id_document')->nullable();
            }

            if (! Schema::hasColumn('city_shipping_costs', 'permit_document')) {
                $table->string('permit_document')->nullable();
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('city_shipping_costs')) {
            return;
        }

        Schema::table('city_shipping_costs', function (Blueprint $table) {
            foreach (['freight_mode', 'invoice_document', 'packing_list_document', 'id_document', 'permit_document'] as $column) {
                if (Schema::hasColumn('city_shipping_costs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
