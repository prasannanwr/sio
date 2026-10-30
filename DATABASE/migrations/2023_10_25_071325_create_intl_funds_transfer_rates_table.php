<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntlFundsTransferRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intl_funds_transfer_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('s_country');
            $table->string('rx_country');
            $table->string('s_currency');
            $table->string('rx_currency');
            $table->double('ex_rate')->default(1);
            $table->enum('calc', ['perc', 'fixed']);
            $table->double('commision')->default(1);
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
        Schema::dropIfExists('intl_funds_transfer_rates');
    }
}
