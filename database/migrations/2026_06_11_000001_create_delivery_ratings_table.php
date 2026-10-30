<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('delivery_ratings')) {
            return;
        }

        Schema::create('delivery_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('delivery_man_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedTinyInteger('rating'); // 1=Poor ... 5=Excellent
            $table->text('comment')->nullable();
            $table->timestamps();

            // One rating per order.
            $table->unique('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_ratings');
    }
};
