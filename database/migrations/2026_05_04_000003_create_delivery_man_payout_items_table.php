<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('delivery_man_payout_items')) {
            return;
        }

        Schema::create('delivery_man_payout_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_man_payout_id')->index();
            $table->unsignedBigInteger('delivery_man_id')->index();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('tracking_id')->nullable()->index();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency', 5)->default('EUR');
            $table->string('status')->default('PENDING')->index();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_man_payout_items');
    }
};
