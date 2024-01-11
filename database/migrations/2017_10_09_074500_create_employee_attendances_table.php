<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_attendance', function (Blueprint $table) {
            $table->increments('employee_attendance_id')->index();
            $table->integer('finger_print_id')->index();
            $table->integer('employee_id')->index();
            $table->text('face_id')->nullable();
            $table->integer('work_shift_id')->nullable()->index();
            $table->integer('branch_id')->nullable()->index();
            $table->string('latitude')->nullable()->index();
            $table->string('longitude')->nullable()->index();
            $table->string('uri')->nullable()->index();
            $table->string('status')->nullable()->index();
            $table->string('inout_status')->nullable()->index()->comment('0-in,1-out,2-in_only');
            $table->dateTime('in_out_time')->index();
            $table->text('check_type')->nullable();
            $table->bigInteger('verify_code')->nullable()->index();
            $table->text('sensor_id')->nullable();
            $table->text('Memoinfo')->nullable();
            $table->text('WorkCode')->nullable();
            $table->text('sn')->nullable();
            $table->integer('UserExtFmt')->nullable()->index();
            $table->string('mechine_sl', 20)->nullable()->index();
            $table->tinyInteger('created_by')->nullable()->index();
            $table->tinyInteger('updated_by')->nullable()->index();
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
        Schema::dropIfExists('employee_attendance');
    }
}
