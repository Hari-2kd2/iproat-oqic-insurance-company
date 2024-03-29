<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('user_id');
            $table->integer('role_id')->unsigned();
            $table->integer('branch_id')->nullable();
            $table->string('email')->nullable();
            $table->string('user_name', 50);
            $table->string('password', 200);
            $table->tinyInteger('status')->default('1');
            $table->rememberToken();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->string('device_employee_id')->nullable();
            $table->softDeletes();
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
        Schema::drop('user');
    }
}
