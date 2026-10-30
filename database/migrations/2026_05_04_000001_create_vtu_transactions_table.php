<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vtu_transactions')) {
            return;
        }

        Schema::create('vtu_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('reference')->unique();
            $table->string('payment_reference')->nullable()->index();
            $table->string('provider_reference')->nullable()->index();
            $table->string('service_type')->index();
            $table->string('provider_code')->index();
            $table->string('product_code')->nullable()->index();
            $table->string('customer_reference')->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->nullable();
            $table->string('currency', 5)->default('NGN');
            $table->string('status')->default('PENDING')->index();
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('provider_response')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vtu_transactions');
    }
};
