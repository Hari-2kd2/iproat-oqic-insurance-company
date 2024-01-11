<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSqlsrvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::connection('sqlsrv')->create('sqlsrvs', function (Blueprint $table) {
        //     $table->string('name')->nullable();
        //     $table->string('place')->nullable();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::connection('sqlsrv')->dropIfExists('sqlsrvs');
    }
}
