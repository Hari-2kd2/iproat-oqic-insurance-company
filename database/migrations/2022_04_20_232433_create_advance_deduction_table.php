<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvanceDeductionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_deduction', function (Blueprint $table) {
            $table->increments('advance_deduction_id');
            $table->integer('employee_id');
            $table->integer('advance_amount');
            $table->string('date_of_advance_given');
            $table->integer('deduction_amouth_per_month');
            $table->integer('no_of_month_to_be_deducted');
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
        Schema::dropIfExists('advance_deduction');
    }
}
