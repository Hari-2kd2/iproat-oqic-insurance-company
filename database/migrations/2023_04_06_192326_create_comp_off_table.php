<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompOffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comp_off', function (Blueprint $table) {
            $table->bigIncrements('comp_off_details_id');
            $table->integer('employee_id')->nullable()->index();
            $table->string('finger_print_id')->index();
            $table->integer('branch_id')->index();
            $table->integer('employee_attendance_id')->index();
            $table->date('off_date')->index();
            $table->date('working_date')->index();
            $table->tinyInteger('off_timing')->default(1)->index();
            $table->time('balance_hour')->index();
            $table->text('comment')->nullable();
            $table->integer('created_by')->index()->nullable();
            $table->integer('updated_by')->index()->nullable();
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
        Schema::dropIfExists('comp_off');
    }
}
