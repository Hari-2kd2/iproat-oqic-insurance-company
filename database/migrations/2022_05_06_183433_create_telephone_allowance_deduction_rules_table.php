<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelephoneAllowanceDeductionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telephone_allowance_deduction_rules', function (Blueprint $table) {
            $table->increments('telephone_allowance_deduction_rule_id');
            $table->integer('cost_per_call');
            $table->integer('limit_per_month');
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
        Schema::dropIfExists('telephone_allowance_deduction_rules');
    }
}
