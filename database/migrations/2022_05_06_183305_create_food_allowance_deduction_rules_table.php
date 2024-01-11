<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoodAllowanceDeductionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_allowance_deduction_rules', function (Blueprint $table) {
            $table->increments('food_allowance_deduction_rule_id');
            $table->integer('breakfast_cost');
            $table->integer('lunch_cost');
            $table->integer('dinner_cost');
            $table->tinyInteger('status')->default(1);
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('food_allowance_deduction_rules');
    }
}
