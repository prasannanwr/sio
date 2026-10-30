<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEUFundsTransferRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_u_funds_transfer_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('s_country_eu');
            $table->string('rx_country_eu');
            $table->enum('calc', ['perc', 'fixed']);
            $table->double('commision')->default(1);
            $table->double('ex_rate')->default(1);
            $table->double('min_amt')->default(0);
            $table->double('max_amt')->default(999);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('e_u_funds_transfer_rates');
    }
}
