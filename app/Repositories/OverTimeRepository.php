<?php
namespace App\Repositories;

use App\Lib\Enumerations\LeaveStatus;
use App\Lib\Enumerations\UserStatus;
use App\Model\Employee;
use App\Model\LeaveApplication;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;

class OverTimeRepository
{

    public function getEmployeeDailyOverTime($date = false)
    {
        if ($date) {
            $data = dateConvertFormtoDB($date);
        } else {
            $data = date("Y-m-d");
        }
        $queryResults = DB::select("call `SP_DailyOverTime`('" . $data . "')");
        $results      = [];
        foreach ($queryResults as $value) {
            $results[$value->department_name][] = $value;
        }
        return $results;
    }
    
    public function getEmployeeMonthlyOverTime($from_date, $to_date, $employee_id)
    {
        $monthlyAttendanceData = DB::select("CALL `SP_MonthlyOverTime`('" . $employee_id . "','" . $from_date . "','" . $to_date . "')");
        $workingDates          = $this->number_of_working_days_date($from_date, $to_date);
        $employeeLeaveRecords  = $this->getEmployeeLeaveRecord($from_date, $to_date, $employee_id);

        $dataFormat = [];
        $tempArray  = [];
        if ($workingDates && $monthlyAttendanceData) {
            foreach ($workingDates as $data) {
                $flag = 0;
                foreach ($monthlyAttendanceData as $value) {
                    if ($data == $value->date) {
                        $flag = 1;
                        break;
                    }
                }
                if ($flag == 0) {
                    $tempArray['employee_id']     = $value->employee_id;
                    $tempArray['fullName']        = $value->fullName;
                    $tempArray['department_name'] = $value->department_name;
                    $tempArray['finger_print_id'] = $value->finger_print_id;
                    $tempArray['date']            = $data;
                    // $tempArray['working_time']    = $value->working_time;
                    // $tempArray['in_time']         = $value->in_time;
                    // $tempArray['out_time']        = $value->out_time;
                    // $tempArray['workingHour']     = $value->workingHour;
                    $tempArray['working_time'] = '';
                    $tempArray['in_time']      = '';
                    $tempArray['out_time']     = '';
                    $tempArray['workingHour']  = '';
                    if (in_array($data, $employeeLeaveRecords)) {
                        $tempArray['action'] = 'Leave';
                    } else {
                        $tempArray['action'] = 'Absence';
                    }
                    $dataFormat[] = $tempArray;
                } else {
                    $tempArray['employee_id']     = $value->employee_id;
                    $tempArray['fullName']        = $value->fullName;
                    $tempArray['department_name'] = $value->department_name;
                    $tempArray['finger_print_id'] = $value->finger_print_id;
                    $tempArray['date']            = $value->date;
                    $tempArray['working_time']    = $value->working_time;
                    $tempArray['in_time']         = $value->in_time;
                    $tempArray['out_time']        = $value->out_time;
                    $tempArray['workingHour']     = $value->workingHour;
                    $tempArray['action']          = '';
                    $dataFormat[]                 = $tempArray;
                }
            }
        }

        return $dataFormat;

    }

    public function number_of_working_days_date($from_date, $to_date)
    {
        $holidays        = DB::select(DB::raw('call SP_getHoliday("' . $from_date . '","' . $to_date . '")'));
        $public_holidays = [];
        foreach ($holidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date   = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $public_holidays[] = $start_date;
                $start_date        = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        $weeklyHolidays     = DB::select(DB::raw('call SP_getWeeklyHoliday()'));
        $weeklyHolidayArray = [];
        foreach ($weeklyHolidays as $weeklyHoliday) {
            $weeklyHolidayArray[] = $weeklyHoliday->day_name;
        }

        $target      = strtotime($from_date);
        $workingDate = [];

        while ($target <= strtotime(date("Y-m-d", strtotime($to_date)))) {
            //get weekly  holiday name
            $timestamp = strtotime(date('Y-m-d', $target));
            $dayName   = date("l", $timestamp);

            if (!in_array(date('Y-m-d', $target), $public_holidays) && !in_array($dayName, $weeklyHolidayArray)) {
                array_push($workingDate, date('Y-m-d', $target));
            }
            if (date('Y-m-d') <= date('Y-m-d', $target)) {
                break;
            }
            $target += (60 * 60 * 24);
        }
        return $workingDate;
    }

    public function getEmployeeLeaveRecord($from_date, $to_date, $employee_id)
    {
        $queryResult = LeaveApplication::select('application_from_date', 'application_to_date')
            ->where('status', LeaveStatus::$APPROVE)
            ->where('application_from_date', '>=', $from_date)
            ->where('application_to_date', '<=', $to_date)
            ->where('employee_id', $employee_id)
            ->get();
        $leaveRecord = [];
        foreach ($queryResult as $value) {
            $start_date = $value->application_from_date;
            $end_date   = $value->application_to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $leaveRecord[] = $start_date;
                $start_date    = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }
        return $leaveRecord;
    }

    public function findOvertimeSummaryReport($month)
    {
        $data      = findMonthToAllDate($month);
        $employees = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'), 'designation_name', 'finger_id', 'employee_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->where('status', UserStatus::$ACTIVE)->get();

        $start_date = $month . '-01';
        $end_date   = date("Y-m-t", strtotime($start_date));

        $dataFormat = [];
        $tempArray  = [];
        $sum        = 0;
        foreach ($employees as $employee) {
            $result = Employee::join('work_shift', "work_shift.work_shift_id", '=', 'employee.work_shift_id')->where('employee_id', $employee->employee_id)->select('start_time', 'end_time')->first();
            foreach ($data as $key => $value) {
                $tempArray['employee_id']      = $employee->employee_id;
                $tempArray['finger_id']        = $employee->finger_id;
                $tempArray['designation_name'] = $employee->designation_name;
                $tempArray['date']             = $value['date'];
                $tempArray['day']              = $value['day'];
                $tempArray['day_name']         = $value['day_name'];
                $attendance                    = DB::table('view_employee_in_out_data')->select('working_time')->where('date', [$value['date']])->where('finger_print_id', $employee->finger_id)->first();
                $tempArray['start_time']       = $result->start_time;
                $tempArray['end_time']         = $result->end_time;

                if (($attendance) != []) {
                    // $tempArray['total_ot']     = DB::table('view_employee_in_out_data')->select('working_time')->where('finger_print_id', [$employee->finger_id])->whereBetween('date', [$start_date, $end_date])->pluck('working_time');
                    $tempArray['status']               = 'true';
                    $tempArray['working_time']         = $attendance->working_time;
                    $startTime                         = new DateTime($result->start_time);
                    $endTime                           = new DateTime($result->end_time);
                    $expected_hour                     = $startTime->diff($endTime);
                    $format_time                       = $expected_hour->format('%H:%I:%S');
                    $tempArray['working_hour'] = new DateTime($format_time);
                    $tempArray['working_time']         = new DateTime($attendance->working_time);
                    // dd($tempArray['working_time'],$tempArray['working_hour']);
                    // if ($working_hour < $working_time) {
                    //     $interval   = $working_hour->diff($working_time);
                    //     $overtime   = $interval->format('%H:%I');
                    //     $explode    = explode(':', $overtime);
                    //     $totalHour  = (int)$explode[0] * 60;
                    //     $totalMinit = (int)$explode[1];
                    //     $total_time = $totalHour + $totalMinit;
                    //     $sum += $total_time;
                    // }
                } else {
                    $tempArray['daily_overtime'] = [$value['date']];
                    $tempArray['status']         = false;
                    $tempArray['working_time']   = '00.00';
                }
                $dataFormat[$employee->fullName][] = $tempArray;
            }
        }
        return $dataFormat;
    }

    public function findOvertimeSummaryReport1($month)
    {
        $data = findMonthToAllDate(Carbon::now()->subMonth(5)->format('Y-m'));
        // dd(Carbon::now()->subMonth(3)->format('Y-m'));
        $employees = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'), 'designation_name', 'finger_id', 'employee_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->where('status', UserStatus::$ACTIVE)->get();

        $start_date = $month . '-01';
        $end_date   = date("Y-m-t", strtotime($start_date));
        // dd($start_date, $end_date);

        $dataFormat = [];
        $tempArray  = [];
        $sum        = 0;
        foreach ($employees as $employee) {
            $samp1                 = $this->getEmployeeMonthlyOverTime($start_date, $end_date, $employee->employee_id);
            $monthlyAttendanceData = DB::select("CALL `SP_MonthlyOverTime`('" . $employee->employee_id . "','" . $start_date . "','" . $end_date . "')");
            $result                = Employee::join('work_shift', "work_shift.work_shift_id", '=', 'employee.work_shift_id')->where('employee_id', $employee->employee_id)->select('start_time', 'end_time')->first();
            foreach ($data as $value) {
                $queryResults = DB::select("call `SP_SummaryOvertime`('" . $value['date'] . "','" . $employee->employee_id . "')");
                $samp         = $this->getEmployeeDailyOverTime($value['date']);
                foreach ($queryResults as $result) {
                    $tempArray['workingDate'] = $result->date;
                    $tempArray['workingTime'] = $result->working_time;
                }
                // dd($value);
                $tempArray['employee_id']      = $employee->employee_id;
                $tempArray['finger_id']        = $employee->finger_id;
                $tempArray['designation_name'] = $employee->designation_name;
                $tempArray['date']             = $value['date'];
                $tempArray['day']              = $value['day'];
                $tempArray['day_name']         = $value['day_name'];

                if ($value['date']) {
                    $tempArray['status']   = 'true';
                    $tempArray['presence'] = 'present';
                } else {
                    $tempArray['status']   = false;
                    $tempArray['presence'] = 'absent';
                }
            }
            $dataFormat[$employee->fullName][] = $tempArray;
        }

        dd($dataFormat);
        return $dataFormat;
    }

    public function ifEmployeeWasLeave($leave, $employee_id, $date)
    {
        $leaveRecord = [];
        $temp        = [];
        foreach ($leave as $value) {
            if ($employee_id == $value->employee_id) {
                $start_date = $value->application_from_date;
                $end_date   = $value->application_to_date;
                while (strtotime($start_date) <= strtotime($end_date)) {
                    $temp['employee_id']     = $employee_id;
                    $temp['date']            = $start_date;
                    $temp['leave_type_name'] = $value->leave_type_name;
                    $leaveRecord[]           = $temp;
                    $start_date              = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
                }
            }
        }

        foreach ($leaveRecord as $val) {
            if (($val['employee_id'] == $employee_id && $val['date'] == $date)) {
                return $val['leave_type_name'];
            }
        }

        return false;
    }

    public function ifHoliday($govtHolidays, $date)
    {
        $govt_holidays = [];
        foreach ($govtHolidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date   = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $govt_holidays[] = $start_date;
                $start_date      = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        foreach ($govt_holidays as $val) {
            if ($val == $date) {
                return true;
            }
        }

        $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday()'));
        $timestamp      = strtotime($date);
        $dayName        = date("l", $timestamp);
        foreach ($weeklyHolidays as $v) {
            if ($v->day_name == $dayName) {
                return true;
            }
        }

        return false;
    }

    public function hasEmployeeAttendance($attendance, $finger_print_id, $date)
    {
        foreach ($attendance as $key => $val) {
            if (($val->finger_print_id == $finger_print_id && $val->date == $date)) {
                return true;
            }
        }
        return false;
    }

    public function hasDailyOverTime($date)
    {
        if ($date) {
            $data = dateConvertFormtoDB($date);
        } else {
            $data = date("Y-m-d");
        }
        $queryResults = DB::select("call `SP_DailyOverTime`('" . $data . "')");
        $results      = [];
        foreach ($queryResults as $value) {
            $results[$value->department_name][] = $value;
        }
        return $results;
    }
    public function DailyOverTime($date = false)
    {
        if ($date) {
            $data = dateConvertFormtoDB($date);
        } else {
            $data = date("Y-m-d");
        }
        $queryResults = DB::select("call `SP_DailyOverTime`('" . $data . "')");
        $results      = [];
        foreach ($queryResults as $value) {
            $results[$value->department_name] = $value;
        }
        return $results;
    }

}
