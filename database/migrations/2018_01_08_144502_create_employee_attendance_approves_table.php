<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeAttendanceApprovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_attendance_approve', function (Blueprint $table) {
            $table->increments('employee_attendance_approve_id')->index();
            $table->integer('employee_id')->index();
            $table->integer('finger_print_id')->index();
            $table->integer('branch_id')->nullable()->index();
            $table->date('date')->index();
            $table->string('in_time')->index();
            $table->string('out_time')->index();
            $table->string('working_hour')->index();
            $table->string('approve_working_hour')->index();
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('employee_attendance_approve');
    }
}
