<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnDutiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('on_duty', function (Blueprint $table) {
            $table->increments('on_duty_id');
            $table->integer('branch_id');
            $table->integer('employee_id');
            $table->date('application_from_date');
            $table->date('application_to_date');
            $table->date('application_date');
            $table->date('approve_date')->nullable();
            $table->decimal('number_of_day');
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
        Schema::dropIfExists('on_duty');
    }
}
