<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->nullable();
            $table->string('ip', 50)->unique()->nullable();
            $table->string('protocol', 10)->nullable();
            $table->text('model')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('device_status');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->integer('port')->nullable();
            $table->string('username')->nullable();
            $table->string('password', 100)->nullable();
            $table->text('devIndex')->nullable();
            $table->text('devResponse')->nullable();
            $table->tinyInteger('verification_status')->nullable();
            $table->tinyInteger('type')->nullable();
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
        Schema::dropIfExists('device');
    }
}
