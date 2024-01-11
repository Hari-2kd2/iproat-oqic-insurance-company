<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftChangeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_change_request', function (Blueprint $table) {
            $table->increments('shift_change_request_id');
            $table->integer('branch_id')->nullable();
            $table->integer('employee_id');
            $table->date('application_from_date');
            $table->date('application_to_date');
            $table->integer('regular_shift');
            $table->integer('work_shift_id');
            $table->date('application_date');
            $table->date('approve_date')->nullable();
            $table->integer('approve_by')->nullable();
            $table->date('reject_date')->nullable();
            $table->integer('reject_by')->nullable();
            $table->text('purpose')->nullable();
            $table->text('remarks')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('shift_change_request');
    }
}
