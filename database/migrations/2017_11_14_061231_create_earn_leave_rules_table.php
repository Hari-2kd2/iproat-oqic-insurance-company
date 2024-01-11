<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEarnLeaveRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('earn_leave_rule', function (Blueprint $table) {
            $table->increments('earn_leave_rule_id')->index();
            $table->integer('branch_id')->nullable()->index();
            $table->integer('for_month')->index();
            $table->float('day_of_earn_leave')->index();
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
        Schema::dropIfExists('earn_leave_rule');
    }
}
