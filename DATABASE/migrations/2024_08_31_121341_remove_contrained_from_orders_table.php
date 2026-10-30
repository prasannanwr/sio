<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveContrainedFromOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        Schema::table('orders', function (Blueprint $table) {
        
            if (Schema::hasColumn('orders', 'customer_id')) {
                $table->dropForeign(['customer_id']);
            }
            if (Schema::hasColumn('orders', 'walk_in_customer_id')) {
                $table->dropForeign(['walk_in_customer_id']);
            }
            if (Schema::hasColumn('orders', 'delivery_customer_id')) {
                $table->dropForeign(['delivery_customer_id']);
            }
            if (Schema::hasColumn('orders', 'courier_id')) {
                $table->dropForeign(['courier_id']);
            }
            if (Schema::hasColumn('orders', 'dispatcher_id')) {
                $table->dropForeign(['dispatcher_id']);
            }
            if (Schema::hasColumn('orders', 'batch_id')) {
                $table->dropForeign(['batch_id']);
            }
            if (Schema::hasColumn('orders', 'shipping_rate_id')) {
                $table->dropForeign(['shipping_rate_id']);
            }
            if (Schema::hasColumn('orders', 'pickup_location_id')) {
                $table->dropForeign(['pickup_location_id']);
            }
            if (Schema::hasColumn('orders', 'delivery_location_id')) {
                $table->dropForeign(['delivery_location_id']);
            }
            if (Schema::hasColumn('orders', 'current_location_id')) {
                $table->dropForeign(['current_location_id']);
            }
        
            
            $table->dropColumn([
                'customer_id',
                'walk_in_customer_id',
                'delivery_customer_id',
                'courier_id',
                'dispatcher_id',
                'batch_id',
                'shipping_rate_id',
                'pickup_location_id',
                'delivery_location_id',
                'current_location_id'
            ]);
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
