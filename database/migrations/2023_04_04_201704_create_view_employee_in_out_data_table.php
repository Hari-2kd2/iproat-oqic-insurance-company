<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewEmployeeInOutDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('view_employee_in_out_data', function (Blueprint $table) {
            $table->bigIncrements('employee_attendance_id')->index();
            $table->integer('branch_id')->nullable()->default(1)->index();
            // $table->integer('approve_over_time_id')->nullable()->index();
            // $table->integer('incentive_details_id')->nullable()->index();
            // $table->integer('comp_off_details_id')->nullable()->index();
            $table->string('finger_print_id', 50)->index();
            $table->date('date')->nullable()->index();
            $table->timestamp('in_time')->nullable()->index();
            $table->timestamp('out_time')->nullable()->index();
            $table->time('working_time')->nullable()->index();
            $table->time('working_hour')->nullable()->index();
            $table->time('over_time')->nullable()->index();
            $table->time('early_by')->nullable()->index();
            $table->time('late_by')->nullable()->index();
            $table->time('early_out')->nullable()->index();
            $table->time('late_out')->nullable()->index();
            $table->text('in_out_time')->nullable();
            $table->string('shift_name', 150)->nullable()->index();
            $table->integer('work_shift_id')->nullable()->index();
            $table->string('device_name', 150)->nullable()->index();
            $table->string('inout_status')->nullable()->index();
            $table->tinyInteger('live_status')->nullable()->default(0)->index();
            $table->tinyInteger('attendance_status')->nullable()->index();
            $table->tinyInteger('earlylate_in_status')->nullable()->index();
            $table->tinyInteger('earlylate_out_status')->nullable()->index();
            $table->tinyInteger('over_time_status')->default(2)->index();
            $table->text('remarks')->nullable();
            $table->tinyInteger('status')->nullable()->default(1)->index();
            $table->integer('created_by')->nullable()->index();
            $table->integer('updated_by')->nullable()->index();
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
        Schema::dropIfExists('view_employee_in_out_data');
    }
}
