<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Our reference; what we send to Monnify and what the app shows the user.
            $table->string('payment_reference')->unique();

            // Reference Monnify gives back after initialization.
            $table->string('transaction_reference')->nullable()->index();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();

            $table->decimal('amount', 15, 2);
            $table->decimal('amount_paid', 15, 2)->nullable();
            $table->decimal('fee', 15, 2)->nullable();
            $table->string('currency', 5)->default('NGN');

            $table->string('payment_description')->nullable();
            $table->string('payment_method')->nullable();   // CARD, ACCOUNT_TRANSFER, USSD, PHONE_NUMBER

            // PENDING, PAID, OVERPAID, PARTIALLY_PAID, FAILED, EXPIRED, USER_CANCELLED
            $table->string('status')->default('PENDING')->index();

            $table->string('checkout_url')->nullable();
            $table->json('metadata')->nullable();
            $table->json('gateway_response')->nullable();   // raw last response from Monnify

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
