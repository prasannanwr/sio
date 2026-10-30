<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntlFundTransferOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intl_fund_transfer_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('walk_in_customer_id')->nullable()->constrained('walk_in_customers');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('dispatcher_id')->nullable()->constrained('dispatchers');
            $table->foreignId('e_u_funds_transfer_rate_id')->nullable()->constrained('e_u_funds_transfer_rates');
            $table->string('tracking_id')->nullable();
            $table->string('rx_surname')->nullable();
            $table->string('rx_name')->nullable();
            $table->string('rx_phone')->nullable();
            $table->string('rx_email')->nullable();
            $table->string('rx_bank_name')->nullable();
            $table->string('rx_bank_routing_no')->nullable();
            $table->string('rx_bank_swift_code')->nullable();
            $table->string('rx_bank_account_name')->nullable();
            $table->string('rx_bank_account_number')->nullable();
            $table->string('rx_country')->nullable();
            $table->string('s_country')->nullable();
            $table->string('rx_currency')->nullable();
            $table->string('s_currency')->nullable();
            $table->double('s_amount')->nullable();
            $table->double('rx_amount')->nullable();
            $table->enum('tx_status', ['unpaid', 'pending', 'done', 'rejected'])->default('unpaid');
            $table->string('tx_reference')->nullable();
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
        Schema::dropIfExists('intl_fund_transfer_orders');
    }
}
