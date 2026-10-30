<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrUpdateVehiclesTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('vehicles')) {
            Schema::create('vehicles', function (Blueprint $table) {
                $table->id();
                $table->string('type', 30);
                $table->string('brand')->nullable();
                $table->string('model')->nullable();
                $table->string('plate_number', 60)->nullable()->unique();
                $table->string('color', 80)->nullable();
                $table->unsignedSmallInteger('year')->nullable();
                $table->json('documents')->nullable();
                $table->unsignedBigInteger('delivery_man_id')->nullable()->index();
                $table->unsignedBigInteger('courier_id')->nullable()->index();
                $table->enum('status', ['active', 'inactive', 'decommisioned'])->default('active');
                $table->timestamps();
            });

            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicles', 'brand')) {
                $table->string('brand')->nullable()->after('type');
            }

            if (! Schema::hasColumn('vehicles', 'model')) {
                $table->string('model')->nullable()->after('brand');
            }

            if (! Schema::hasColumn('vehicles', 'plate_number')) {
                $table->string('plate_number', 60)->nullable()->unique()->after('model');
            }

            if (! Schema::hasColumn('vehicles', 'color')) {
                $table->string('color', 80)->nullable()->after('plate_number');
            }

            if (! Schema::hasColumn('vehicles', 'year')) {
                $table->unsignedSmallInteger('year')->nullable()->after('color');
            }

            if (! Schema::hasColumn('vehicles', 'documents')) {
                $table->json('documents')->nullable()->after('year');
            }

            if (! Schema::hasColumn('vehicles', 'delivery_man_id')) {
                $table->unsignedBigInteger('delivery_man_id')->nullable()->index()->after('documents');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('vehicles')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            foreach (['delivery_man_id', 'documents', 'year', 'color', 'plate_number', 'model', 'brand'] as $column) {
                if (Schema::hasColumn('vehicles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
