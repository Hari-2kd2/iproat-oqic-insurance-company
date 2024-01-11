<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_log', function (Blueprint $table) {
            $table->increments('attendance_log_id')->index();
            $table->integer('branch_id')->nullable()->index();
            $table->string('employeeId')->nullable()->index();
            $table->date('date')->index();
            $table->time('time')->index();
            $table->string('deviceSerial')->nullable()->index();
            $table->string('deviceId')->nullable()->index();
            $table->string('locationName')->nullable()->index();
            $table->string('locationId')->nullable()->index();
            $table->string('mode')->nullable()->index();
            $table->tinyInteger('type')->nullable()->index();
            $table->string('deviceName')->nullable()->index();
            $table->string('lateEntry')->nullable()->index();
            $table->string('companyDisplayId')->nullable()->index();
            $table->text('companyName')->nullable();
            $table->text('lastEvaluatedKey')->nullable();
            $table->integer('size')->nullable();
            $table->tinyInteger('status')->nullable()->default(0)->index();
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
        Schema::dropIfExists('attendance_log');
    }
}
