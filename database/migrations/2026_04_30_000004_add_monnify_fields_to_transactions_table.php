<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->string('payment_reference')->unique();
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
                $table->string('payment_method')->nullable();
                $table->string('status')->default('PENDING')->index();
                $table->string('checkout_url')->nullable();
                $table->json('metadata')->nullable();
                $table->json('gateway_response')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->unique();
            }

            if (! Schema::hasColumn('transactions', 'amount')) {
                $table->decimal('amount', 15, 2)->nullable();
            }

            if (! Schema::hasColumn('transactions', 'amount_paid')) {
                $table->decimal('amount_paid', 15, 2)->nullable();
            }

            if (! Schema::hasColumn('transactions', 'fee')) {
                $table->decimal('fee', 15, 2)->nullable();
            }

            if (! Schema::hasColumn('transactions', 'currency')) {
                $table->string('currency', 5)->default('NGN');
            }

            if (! Schema::hasColumn('transactions', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }

            if (! Schema::hasColumn('transactions', 'customer_email')) {
                $table->string('customer_email')->nullable();
            }

            if (! Schema::hasColumn('transactions', 'customer_phone')) {
                $table->string('customer_phone')->nullable();
            }

            if (! Schema::hasColumn('transactions', 'payment_description')) {
                $table->string('payment_description')->nullable();
            }

            if (! Schema::hasColumn('transactions', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }

            if (! Schema::hasColumn('transactions', 'status')) {
                $table->string('status')->default('PENDING')->index();
            }

            if (! Schema::hasColumn('transactions', 'checkout_url')) {
                $table->string('checkout_url')->nullable();
            }

            if (! Schema::hasColumn('transactions', 'metadata')) {
                $table->json('metadata')->nullable();
            }

            if (! Schema::hasColumn('transactions', 'gateway_response')) {
                $table->json('gateway_response')->nullable();
            }

            if (! Schema::hasColumn('transactions', 'paid_at')) {
                $table->timestamp('paid_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('transactions')) {
            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            foreach ([
                'payment_reference',
                'amount',
                'amount_paid',
                'fee',
                'currency',
                'customer_name',
                'customer_email',
                'customer_phone',
                'payment_description',
                'payment_method',
                'status',
                'checkout_url',
                'metadata',
                'gateway_response',
                'paid_at',
            ] as $column) {
                if (Schema::hasColumn('transactions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
