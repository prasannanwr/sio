<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('phone_number');
            $table->foreignId('country')->constrained('operation_countries');
            $table->enum('relationship', ['father', 'mother', 'brother', 'sister', 'uncle', 'aunt']);
            $table->enum('gender', ['male', 'female']);
            $table->text('extra_datas')->nullable();
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
        Schema::dropIfExists('beneficiaries');
    }
}
