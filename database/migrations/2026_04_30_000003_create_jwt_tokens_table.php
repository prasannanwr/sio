<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJwtTokensTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('jwt_tokens')) {
            return;
        }

        Schema::create('jwt_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('jti')->unique();
            $table->string('token_hash', 64)->unique();
            $table->dateTime('expires_at')->index();
            $table->dateTime('last_used_at')->nullable();
            $table->dateTime('revoked_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jwt_tokens');
    }
}
