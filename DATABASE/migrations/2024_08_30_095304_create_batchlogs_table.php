<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batchlogs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ship_from_country_id')->nullable();
            $table->bigInteger('ship_from_city_id')->nullable();
            $table->bigInteger('ship_to_country_id')->nullable();
            $table->bigInteger('ship_to_city_id')->nullable();
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
        Schema::dropIfExists('batchlogs');
    }
}
