<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManualAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_attendance', function (Blueprint $table) {
            $table->bigIncrements('primary_id')->index();
            $table->integer('branch_id')->nullable()->index();
            $table->string('ID', 50)->index();
            $table->string('type', 11)->nullable()->index();
            $table->timestamp('datetime')->index();
            $table->tinyInteger('status')->default(0);
            $table->text('remarks')->nullable();
            $table->string('device_name')->nullable()->index();
            $table->string('devuid')->nullable()->index();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('manual_attendance');
    }
}
