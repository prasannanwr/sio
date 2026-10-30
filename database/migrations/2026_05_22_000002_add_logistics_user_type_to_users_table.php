<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLogisticsUserTypeToUsersTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        DB::statement("ALTER TABLE `users` MODIFY COLUMN `user_type` ENUM('user', 'courier', 'dispatcher', 'admin', 'mobile', 'agent', 'subAdmin', 'logistics') NOT NULL DEFAULT 'user'");
    }

    public function down()
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        DB::statement("ALTER TABLE `users` MODIFY COLUMN `user_type` ENUM('user', 'courier', 'dispatcher', 'admin', 'mobile', 'agent', 'subAdmin') NOT NULL DEFAULT 'user'");
    }
}
