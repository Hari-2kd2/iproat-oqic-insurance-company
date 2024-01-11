<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHolidayDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holiday_details', function (Blueprint $table) {
            $table->increments('holiday_details_id')->index();
            $table->integer('branch_id')->nullable()->index();
            $table->integer('holiday_id')->unsigned()->index();
            $table->date('from_date')->index();
            $table->date('to_date')->index();
            $table->string('leave_timing')->nullable();
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('holiday_details');
    }
}
