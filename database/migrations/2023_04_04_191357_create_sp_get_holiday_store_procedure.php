<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSPGetHolidayStoreProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS SP_getHoliday; CREATE PROCEDURE SP_getHoliday(IN fromDate DATE,IN toDate DATE)
        BEGIN
        SELECT from_date,to_date FROM holiday_details WHERE from_date >= fromDate AND to_date <=toDate;
        END');
    }

    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS SP_getHoliday');
    }
}
