<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_funds', function (Blueprint $table) {
            $table->id();
            $table->string('transId');
            $table->foreignId('user_id')->constrained('users');
            $table->double('amount')->default(0);
            $table->string('currency')->default('EUR');
            $table->string('description')->nullable();
            $table->enum('flag', ['debit', 'credit'])->default('debit'); //debit for positive values and credit for negative values
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_funds');
    }
}
