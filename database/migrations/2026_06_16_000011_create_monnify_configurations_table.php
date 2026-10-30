<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('monnify_configurations')) {
            return;
        }

        Schema::create('monnify_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('api_key');
            $table->text('secret_key');            // encrypted
            $table->string('contract_code');
            $table->string('base_url')->default('https://sandbox.monnify.com');
            $table->string('currency', 5)->default('NGN');
            $table->text('webhook_secret')->nullable(); // encrypted
            $table->string('redirect_url')->nullable();
            $table->string('source_account_number')->nullable(); // disbursement source account
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monnify_configurations');
    }
};
