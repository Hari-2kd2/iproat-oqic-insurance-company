<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalaryDetailsToLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_details_to_leave', function (Blueprint $table) {
            $table->increments('salary_details_to_leave_id')->index();
            $table->integer('salary_details_id')->index();
            $table->integer('leave_type_id')->index();
            $table->integer('num_of_day')->index();
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
        Schema::dropIfExists('salary_details_to_leave');
    }
}
