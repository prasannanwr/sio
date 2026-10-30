<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->foreignId('origin_id')->nullable()->constrained('locations');
            $table->foreignId('destination_id')->nullable()->constrained('locations');
            $table->float('weight_start')->nullable();
            $table->float('weight_end')->nullable();
            $table->float('price')->nullable();
            $table->float('length')->nullable();
            $table->float('width')->nullable();
            $table->float('height')->nullable();
            $table->text('desc')->nullable();
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
        Schema::dropIfExists('shipping_rates');
    }
}
