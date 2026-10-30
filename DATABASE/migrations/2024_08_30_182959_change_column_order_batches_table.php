<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnOrderBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_batches', function (Blueprint $table) {
            $table->dropForeign('order_batches_dispatcher_id_foreign');
            $table->dropColumn('dispatcher_id');
            $table->dropForeign('order_batches_location_id_foreign');
            $table->dropColumn('location_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_batches', function (Blueprint $table) {
            //
        });
    }
}
