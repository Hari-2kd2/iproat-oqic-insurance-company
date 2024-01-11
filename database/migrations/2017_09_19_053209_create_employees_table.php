<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->increments('employee_id')->index();
            $table->integer('user_id')->unsigned()->index();
            $table->string('finger_id')->unique()->index();
            $table->integer('department_id')->default(1)->index();
            $table->integer('designation_id')->default(1)->index();
            $table->integer('branch_id')->unsigned()->nullable()->index();
            $table->tinyInteger('incentive')->default(0)->nullable()->index();
            $table->tinyInteger('work_shift')->default(0)->nullable()->index();
            $table->integer('supervisor_id')->nullable()->index();
            $table->integer('work_shift_id')->unsigned()->index()->default(1);
            $table->string('weekoff_updated_at', 50)->nullable()->index();
            $table->string('esi_card_number', 30)->nullable()->index();
            $table->string('pf_account_number', 30)->nullable()->index();
            $table->integer('pay_grade_id')->unsigned()->nullable()->default(0)->index();
            $table->integer('hourly_salaries_id')->unsigned()->nullable()->default(0)->index();
            $table->string('email', 50)->unique()->nullable()->index();
            $table->string('first_name', 30)->index();
            $table->string('last_name', 30)->nullable()->index();
            $table->date('date_of_birth')->index();
            $table->date('date_of_joining')->index();
            $table->date('date_of_leaving')->nullable()->index();
            $table->string('gender', 10)->index();
            $table->string('religion', 50)->nullable()->index();
            $table->string('nationality', 100)->nullable()->index();
            $table->string('marital_status', 10)->nullable()->index();
            $table->string('photo', 250)->nullable()->index();
            $table->text('address')->nullable();
            $table->text('emergency_contacts')->nullable();
            $table->string('document_title')->nullable()->index();
            $table->string('document_name')->nullable()->index();
            $table->date('document_expiry')->nullable()->index();
            $table->string('document_title2')->nullable()->index();
            $table->string('document_name2')->nullable()->index();
            $table->date('document_expiry2')->nullable()->index();
            $table->string('document_title3')->nullable()->index();
            $table->string('document_name3')->nullable()->index();
            $table->date('document_expiry3')->nullable()->index();
            $table->string('document_title4')->nullable()->index();
            $table->string('document_name4')->nullable()->index();
            $table->date('document_expiry4')->nullable()->index();
            $table->string('document_title5')->nullable()->index();
            $table->string('document_name5')->nullable()->index();
            $table->date('document_expiry5')->nullable()->index();
            $table->string('phone')->index();
            $table->tinyInteger('status')->default(1)->index();
            $table->tinyInteger('salary_limit')->default(0)->comment('0-lessthen 20000,1-morethen 20000')->index();
            $table->tinyInteger('permanent_status')->default(0)->index();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->string('device_employee_id')->nullable()->index();
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
        Schema::dropIfExists('employee');
    }
}
