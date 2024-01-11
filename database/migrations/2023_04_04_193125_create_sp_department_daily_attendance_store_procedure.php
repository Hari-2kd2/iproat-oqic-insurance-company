<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSpDepartmentDailyAttendanceStoreProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS SP_DepartmentDailyAttendance; CREATE PROCEDURE SP_DepartmentDailyAttendance(IN input_date DATE,IN department_id INT(10),IN attendance_status INT(10))
            
            BEGIN

            select employee.employee_id,employee.supervisor_id,designation.designation_name,department.department_name,branch.branch_name,employee.photo,CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName,department_name,
            view_employee_in_out_data.employee_attendance_id,view_employee_in_out_data.finger_print_id,view_employee_in_out_data.date,view_employee_in_out_data.working_time,
            view_employee_in_out_data.device_name, view_employee_in_out_data.shift_name, view_employee_in_out_data.late_by, view_employee_in_out_data.early_by,
            view_employee_in_out_data.over_time, view_employee_in_out_data.in_out_time, view_employee_in_out_data.attendance_status,view_employee_in_out_data.over_time_status,
            DATE_FORMAT(view_employee_in_out_data.in_time,\'%h:%i %p\') AS in_time,DATE_FORMAT(view_employee_in_out_data.out_time,\'%h:%i %p\') AS out_time,
		    TIME_FORMAT( work_shift.late_count_time, \'%H:%i:%s\' ) as lateCountTime,
	        (SELECT CASE WHEN DATE_FORMAT(MIN(view_employee_in_out_data.in_time),\'%H:%i:00\')  > lateCountTime
            THEN \'Yes\'
            ELSE \'No\' END) AS  ifLate,
            (SELECT CASE WHEN TIMEDIFF((DATE_FORMAT(MIN(view_employee_in_out_data.in_time),\'%H:%i:%s\')),work_shift.late_count_time)  > \'0\'
            THEN TIMEDIFF((DATE_FORMAT(MIN(view_employee_in_out_data.in_time),\'%H:%i:%s\')),work_shift.late_count_time)
            ELSE \'00:00:00\' END) AS  totalLateTime,
            TIMEDIFF((DATE_FORMAT(work_shift.`end_time`,\'%H:%i:%s\')),work_shift.`start_time`) AS workingHour
            from employee
            inner join view_employee_in_out_data on view_employee_in_out_data.finger_print_id = employee.finger_id
            inner join department on department.department_id = employee.department_id
            inner join designation on designation.designation_id = employee.designation_id
            inner join branch on branch.branch_id = employee.branch_id
            JOIN work_shift on work_shift.work_shift_id = employee.work_shift_id
            where ( `date`=input_date)AND(employee.department_id=department_id OR department_id="")AND(view_employee_in_out_data.attendance_status=attendance_status OR attendance_status="")
            GROUP BY view_employee_in_out_data.finger_print_id ORDER BY view_employee_in_out_data.finger_print_id;

            END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS SP_DepartmentDailyAttendance');
    }
}
