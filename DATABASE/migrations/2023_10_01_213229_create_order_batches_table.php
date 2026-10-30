<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_batches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('dispatcher_id')->nullable()->constrained('dispatchers');
            $table->enum('status', ['assigned', 'in_transit', 'delivered', 'cancelled'])->default('assigned');
            $table->foreignId('location_id')->nullable()->constrained('locations');
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
        Schema::dropIfExists('order_batches');
    }
}
