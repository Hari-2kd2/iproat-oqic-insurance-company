<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeAccessControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_access_control', function (Blueprint $table) {
            $table->increments('id')->index();
            $table->integer('employee')->nullable()->index();
            $table->integer('department')->nullable()->index();
            $table->integer('branch_id')->nullable()->index();
            $table->integer('device')->nullable()->index();
            $table->integer('user_id')->nullable()->index();
            $table->string('device_employee_id')->nullable()->index();
            $table->tinyInteger('status')->nullable()->index();
            $table->integer('created_by')->nullable()->index();
            $table->integer('updated_by')->nullable()->index();
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
        Schema::dropIfExists('employee_access_control');
    }
}
