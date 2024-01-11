<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApproveOverTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approve_over_time', function (Blueprint $table) {
            $table->bigIncrements('approve_over_time_id');
            $table->integer('branch_id')->nullable()->index();
            $table->integer('finger_print_id')->nullable()->index();
            $table->integer('employee_attendance_id')->index();
            $table->date('date')->nullable()->index();
            $table->time('actual_overtime')->nullable()->index();
            $table->time('approved_overtime')->nullable()->index();
            $table->text('remark');
            $table->tinyInteger('status')->nullable()->index()->default(0);
            $table->integer('updated_by')->nullable()->index();
            $table->integer('created_by')->nullable()->index();
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
        Schema::dropIfExists('approve_over_time');
    }
}
