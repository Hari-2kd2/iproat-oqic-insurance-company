<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSPWeeklyHolidayStoreProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS SP_getWeeklyHoliday; CREATE  PROCEDURE SP_getWeeklyHoliday(IN emp_id INT(10),IN from_month varchar(10))
        BEGIN
        select day_name , employee_id, weekoff_days from  weekly_holiday where status=1 and employee_id = emp_id and month >= from_month;
        END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS SP_getWeeklyHoliday');
    }
}
