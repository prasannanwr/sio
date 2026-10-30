<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('delivery_man_location_pings')) {
            return;
        }

        Schema::create('delivery_man_location_pings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_man_id');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();

            $table->index(['delivery_man_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_man_location_pings');
    }
};
