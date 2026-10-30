<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalkInCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('walk_in_customers', function (Blueprint $table) {
            $table->id();
            $table->string('surname');
            $table->string('name');
            $table->date('birthDate')->nullable();
            $table->date('birthPlace')->nullable();
            $table->string('gender')->nullable();
            $table->string('belfioreCode')->nullable();
            $table->string('doc_type')->nullable();
            $table->string('doc_num')->nullable();
            $table->string('doc_front')->nullable();
            $table->string('doc_back')->nullable();
            $table->string('tax_code')->nullable();
            $table->enum('kyc_status', ['pending', 'approved', 'rejected'])->default('pending');
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
        Schema::dropIfExists('walk_in_customers');
    }
}
