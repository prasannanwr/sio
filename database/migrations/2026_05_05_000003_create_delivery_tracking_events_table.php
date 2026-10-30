<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('delivery_tracking_events')) {
            return;
        }

        Schema::create('delivery_tracking_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('tracking_id')->index();
            $table->unsignedBigInteger('delivery_man_id')->nullable()->index();
            $table->unsignedBigInteger('performed_by')->nullable()->index();
            $table->string('old_status')->nullable();
            $table->string('new_status')->index();
            $table->string('old_delivery_man_status')->nullable();
            $table->string('new_delivery_man_status')->nullable();
            $table->unsignedBigInteger('current_location_id')->nullable();
            $table->unsignedBigInteger('current_location_country_id')->nullable();
            $table->unsignedBigInteger('current_location_city_id')->nullable();
            $table->text('note')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_tracking_events');
    }
};
