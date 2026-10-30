<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('delivery_customer_id')->nullable()->constrained('customers');
            $table->foreignId('courier_id')->nullable()->constrained('couriers');
            $table->foreignId('dispatcher_id')->nullable()->constrained('dispatchers');
            $table->foreignId('shipping_rate_id')->nullable()->constrained('shipping_rates');
            $table->enum('status', ['unpaid', 'placed', 'assigned', 'in_transit', 'delivered', 'cancelled'])->default('unpaid');
            $table->foreignId('pickup_location_id')->nullable()->constrained('locations');
            $table->foreignId('delivery_location_id')->nullable()->constrained('locations');
            $table->foreignId('current_location_id')->nullable()->constrained('locations');
            $table->string('tracking_id')->unique();
            $table->timestamp('pickup_time')->nullable();
            $table->timestamp('delivery_time')->nullable();
            $table->string('delivery_name')->nullable();
            $table->string('delivery_email')->nullable();
            $table->string('delivery_phone')->nullable();
            $table->string('delivery_phone_alt')->nullable();
            $table->text('delivery_address1')->nullable();
            $table->text('delivery_address2')->nullable();
            $table->string('delivery_zip')->nullable();
            $table->string('delivery_city')->nullable();
            $table->string('delivery_state')->nullable();
            $table->string('delivery_country')->nullable();
            $table->string('pickup_name')->nullable();
            $table->string('pickup_phone')->nullable();
            $table->string('pickup_email')->nullable();
            $table->string('pickup_phone_alt')->nullable();
            $table->text('pickup_address1')->nullable();
            $table->text('pickup_address2')->nullable();
            $table->string('pickup_zip')->nullable();
            $table->string('pickup_city')->nullable();
            $table->string('pickup_state')->nullable();
            $table->string('pickup_country')->nullable();
            $table->date('return_date')->nullable();
            $table->string('cond_of_goods')->nullable();
            $table->float('val_of_goods')->nullable();
            $table->string('val_cur')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
