<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeOverTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_overtime', function (Blueprint $table) {
            $table->increments('employee_over_time_id')->index();
            $table->string('date')->index();
            $table->text('employee_id');
            $table->text('work_shift_id');
            $table->string('Overtime_duration')->index();
            $table->tinyInteger('status')->default(1)->nullable()->index();
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
        Schema::dropIfExists('employee_overtime');
    }
}
