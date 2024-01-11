<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeeklyHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weekly_holiday', function (Blueprint $table) {
            $table->increments('week_holiday_id')->index();
            $table->integer('branch_id')->nullable()->index();
            $table->integer('employee_id')->nullable()->index();
            $table->string('month')->nullable()->index();
            $table->string('day_name')->index();
            $table->string('weekoff_days')->nullable()->index();
            $table->tinyInteger('status')->default('1')->index();
            $table->tinyInteger('created_by')->nullable();
            $table->tinyInteger('updated_by')->nullable();
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
        Schema::dropIfExists('weekly_holiday');
    }
}
