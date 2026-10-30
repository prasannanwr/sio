<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('delivery_man_payout_logs')) {
            return;
        }

        Schema::create('delivery_man_payout_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_man_payout_id')->nullable()->index();
            $table->string('payout_reference')->nullable()->index();
            $table->string('action')->index();
            $table->unsignedBigInteger('performed_by')->nullable()->index();
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->text('message')->nullable();
            $table->json('payload')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_man_payout_logs');
    }
};
