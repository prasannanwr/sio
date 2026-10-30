<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKycTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kyc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('document_type_id')->nullable()->constrained('kyc_document_type');
            $table->string('document_front')->nullable();
            $table->string('document_back')->nullable();
            $table->text('selfie')->nullable();
            $table->foreignId('proof_of_address_type_id')->nullable()->constrained('kyc_address_proof_types');
            $table->text('proof_of_address')->nullable();
            $table->integer('kyc_level')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
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
        Schema::dropIfExists('kyc');
    }
}
