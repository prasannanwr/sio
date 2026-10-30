<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthOtpsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('auth_otps')) {
            return;
        }

        Schema::create('auth_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('identifier')->index();
            $table->string('channel', 20);
            $table->string('purpose', 40)->index();
            $table->string('code_hash');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->timestamps();

            $table->index(['identifier', 'purpose', 'consumed_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('auth_otps');
    }
}
