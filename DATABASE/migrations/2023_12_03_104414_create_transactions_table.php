<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->enum('transaction_type', ['transfer'])->default('transfer');
            $table->foreignId('beneficiary_id')->constrained();
            $table->foreignId('beneficiary_account_id')->constrained();
            $table->string('transaction_id');
            $table->double('transaction_amount');
            $table->string('beneficiary_account_number');
            $table->string('beneficiary_account_name');
            $table->string('beneficiary_bank_name');
            $table->string('beneficiary_bank_code');
            $table->string('payment_provider');
            $table->enum('transaction_status', ['success', 'pending', 'failed', 'refunded'])->default('success');
            $table->string('transaction_reference')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
