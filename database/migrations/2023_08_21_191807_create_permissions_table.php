<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission', function (Blueprint $table) {
            $table->increments('permission_id');
            $table->integer('branch_id');
            $table->integer('employee_id');
            $table->date('application_from_time');
            $table->date('application_to_time');
            $table->date('application_date');
            $table->date('approve_date');
            $table->time('duration');
            $table->integer('approve_by');
            $table->date('reject_date');
            $table->integer('reject_by');
            $table->text('purpose');
            $table->text('remarks');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('permission');
    }
}
