<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateEnumValuesInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `user_type` ENUM('user', 'courier', 'dispatcher','admin','mobile','agent') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `user_type` ENUM('user', 'courier', 'dispatcher','admin','mobile') NOT NULL");
        });
    }
}
