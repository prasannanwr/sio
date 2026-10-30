<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NormalizeJwtTokenDateColumns extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('jwt_tokens')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (Schema::hasColumn('jwt_tokens', 'expires_at')) {
            DB::statement('ALTER TABLE jwt_tokens MODIFY expires_at DATETIME NOT NULL');
        }

        if (Schema::hasColumn('jwt_tokens', 'last_used_at')) {
            DB::statement('ALTER TABLE jwt_tokens MODIFY last_used_at DATETIME NULL');
        }

        if (Schema::hasColumn('jwt_tokens', 'revoked_at')) {
            DB::statement('ALTER TABLE jwt_tokens MODIFY revoked_at DATETIME NULL');
        }
    }

    public function down()
    {
        if (! Schema::hasTable('jwt_tokens')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        if (Schema::hasColumn('jwt_tokens', 'expires_at')) {
            DB::statement('ALTER TABLE jwt_tokens MODIFY expires_at TIMESTAMP NOT NULL');
        }

        if (Schema::hasColumn('jwt_tokens', 'last_used_at')) {
            DB::statement('ALTER TABLE jwt_tokens MODIFY last_used_at TIMESTAMP NULL');
        }

        if (Schema::hasColumn('jwt_tokens', 'revoked_at')) {
            DB::statement('ALTER TABLE jwt_tokens MODIFY revoked_at TIMESTAMP NULL');
        }
    }
}
