<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeFoodAndTelephoneDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_food_and_telephone_deductions', function (Blueprint $table) {
            $table->increments('employee_food_and_telephone_deduction_id');
            $table->string('month_of_deduction',20);
            $table->integer('finger_print_id');
            $table->integer('employee_id');
            $table->integer('food_allowance_deduction_rule_id')->default(1);
            $table->integer('telephone_allowance_deduction_rule_id')->default(1);
            $table->integer('call_consumed_per_month')->default(0);
            $table->integer('breakfast_count')->default(0);
            $table->integer('lunch_count')->default(0);
            $table->integer('dinner_count')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->text('remarks')->default(null);
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
        Schema::dropIfExists('employee_food_and_telephone_deductions');
    }
}
