<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_application', function (Blueprint $table) {
            $table->increments('leave_application_id')->index();
            $table->integer('employee_id')->unsigned()->index();
            $table->integer('branch_id')->nullable()->index();
            $table->integer('leave_type_id')->unsigned()->index();
            $table->date('application_from_date')->index();
            $table->date('application_to_date')->index();
            $table->date('application_date')->index();
            $table->integer('number_of_day')->index();
            $table->date('approve_date')->nullable()->index();
            $table->date('reject_date')->nullable()->index();
            $table->integer('approve_by')->nullable()->index();
            $table->integer('reject_by')->nullable()->index();
            $table->text('purpose');
            $table->text('remarks')->nullable();
            $table->string('status')->default('1')->index()->comment = "status(1,2,3) = Pending,Approve,Reject";
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
        Schema::dropIfExists('leave_application');
    }
}
