<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::dropIfExists('agents');
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('agency_type')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_alt')->nullable();
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->string('zip')->nullable();
            $table->bigInteger('city_id')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->boolean('status')->nullable();
            $table->string('attachment_path')->nullable();  
            $table->string('front_attachment')->nullable();
            $table->bigInteger('location_id')->nullable();            
            $table->string('business_name')->nullable();
            $table->string('tax_id_code')->nullable();
            $table->string('vat_no')->nullable();
            $table->string('pec')->nullable();
            $table->string('sdi')->nullable();
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
        Schema::dropIfExists('agents');
    }
}
