<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('delivery_man_payouts')) {
            return;
        }

        Schema::create('delivery_man_payouts', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->unsignedBigInteger('delivery_man_id')->index();
            $table->unsignedBigInteger('processed_by')->nullable()->index();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->unsignedInteger('orders_count')->default(0);
            $table->string('currency', 5)->default('EUR');
            $table->string('status')->default('PENDING')->index();
            $table->string('payout_method')->nullable();
            $table->string('payment_reference')->nullable()->index();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_man_payouts');
    }
};
