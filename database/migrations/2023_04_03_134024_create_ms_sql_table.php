<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMsSqlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_sql', function (Blueprint $table) {
            $table->increments('primary_id')->index();
            $table->integer('branch_id')->nullable()->index();
            $table->integer('local_primary_id')->nullable()->index();
            $table->integer('evtlguid')->nullable()->index();
            $table->string('ID')->nullable()->index();
            $table->string('type')->nullable()->index();
            $table->timestamp('datetime')->index();
            $table->tinyInteger('status')->default(0)->index();
            $table->integer('employee')->nullable()->index();
            $table->integer('device')->nullable()->index();
            $table->string('device_employee_id')->nullable()->index();
            $table->text('sms_log')->nullable();
            $table->string('device_name')->nullable()->index();
            $table->string('devuid')->nullable()->index();
            $table->tinyInteger('live_status')->nullable()->index();
            $table->timestamp('punching_time')->nullable()->index();
            $table->tinyInteger('created_by')->nullable()->index();
            $table->tinyInteger('updated_by')->nullable()->index();
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
        Schema::dropIfExists('ms_sql');
    }
}
