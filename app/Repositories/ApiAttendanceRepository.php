<?php

namespace App\Repositories;

use App\Lib\Enumerations\AppConstant;
use App\Lib\Enumerations\LeaveStatus;
use App\Model\EmployeeAttendance;
use App\Model\LeaveApplication;
use App\Model\MsSql;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiAttendanceRepository
{

    public function makeEmployeeAttendacneInformationDataFormat($uri, $data)
    {
        try {
            $recent_data = EmployeeAttendance::where('finger_print_id', $data['finger_id'])->whereDate('in_out_time', date('Y-m-d', \strtotime($data['datetime'])))->orderByDesc('employee_attendance_id')->first();

            if (count($recent_data) == 0) {
                $employeeData['check_type'] = 'IN';
            } elseif ($recent_data['check_type'] == 'OUT') {
                $employeeData['check_type'] = 'IN';
            } else {
                $employeeData['check_type'] = 'OUT';
            }

            // if ($uri == 'api/mobile/attendance/employee_attendance_in') {
            //     $employeeData['check_type'] = 'IN';
            // } elseif ($uri == 'api/mobile/attendance/employee_attendance_out') {
            //     $employeeData['check_type'] = 'OUT';
            // } else {
            //     $employeeData['check_type'] = 'None';
            // }

            $employeeData['in_out_time'] = date('Y-m-d H:i:s', \strtotime($data['datetime']));

            $employeeData['finger_print_id'] = $data['finger_id'];

            $employeeData['employee_id'] = $data['employee_id'];

            $employeeData['latitude'] = $data['latitude'];

            $employeeData['longitude'] = $data['longitude'];

            $employeeData['uri'] = 'api';

            return $employeeData;
        } catch (\Throwable $th) {
            return false;
        }
    }
    public function makeBulkEmployeeAttendacneInformationDataFormat($data)
    {
        if ($data->check_type == '0') {
            $employeeData['check_type'] = 'IN';
        } else {
            $employeeData['check_type'] = 'OUT';
        }

        $latlngOfCompany = [
            'lat' => '13.5119982',
            'lng' => '80.0139697',
        ];

        // $latlngOfCompany = [
        //     'lat' => '10.8066651',
        //     'lng' => '78.7229832',
        // ];

        $latlngOfCurrentLocation = [
            'lat' => $data->latitude,
            'lng' => $data->longitude,
        ];

        // info($latlngOfCompany);
        // info($latlngOfCurrentLocation);

        $dataFormat = $this->getDistanceBetweenPoints($latlngOfCompany, $latlngOfCurrentLocation);

        info($dataFormat['meters']);

        if ($dataFormat['meters'] > AppConstant::$DISTANCE) {
            $inout_status = 'O';
        } else {
            $inout_status = 'I';
        }

        $employeeData['in_out_time'] = date('Y-m-d H:i:s', \strtotime($data->datetime));

        $employeeData['finger_print_id'] = $data->finger_id;

        $employeeData['employee_id'] = $data->employee_id;

        $employeeData['latitude'] = $data->latitude;

        $employeeData['longitude'] = $data->longitude;

        $employeeData['uri'] = 'api';

        return $employeeData;
    }

    public function makeEmployeeAttendacneInformationDataFormatSample($data)
    {
        $checkAttendance = EmployeeAttendance::whereDate('in_out_time', date('Y-m-d', \strtotime(Carbon::now())))
            ->where('employee_id', $data['employee_id'])->count();

        if ($checkAttendance % 2 != 0) {
            $employeeData['check_type'] = 'OUT';
        } else {
            $employeeData['check_type'] = 'IN';
        }

        $employeeData['in_out_time'] = $data['datetime'];

        $employeeData['finger_print_id'] = $data['finger_id'];

        $employeeData['employee_id'] = $data['employee_id'];

        $employeeData['latitude'] = $data['latitude'];

        $employeeData['longitude'] = $data['longitude'];

        $employeeData['uri'] = 'api';

        return $employeeData;
    }

    public function getEmployeeDailyAttendance($date = false)
    {
        if ($date) {
            $data = dateConvertFormtoDB($date);
        } else {
            $data = date("Y-m-d");
        }
        $queryResults = DB::select("call `SP_DailyAttendance`('" . $data . "')");

        $results = [];
        foreach ($queryResults as $key => $value) {
            $results = $value;
        }
        return $results;
    }

    public function getEmployeeMonthlyAttendance($from_date, $to_date, $employee_id)
    {
        try {
            $monthlyAttendanceData = DB::select("CALL `SP_monthlyAttendance`('" . $employee_id . "','" . $from_date . "','" . $to_date . "')");
            $workingDates = $this->number_of_working_days_date($from_date, $to_date);
            $employeeLeaveRecords = $this->getEmployeeLeaveRecord($from_date, $to_date, $employee_id);

            $dataFormat = [];
            $tempArray = [];
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
                        $tempArray['employee_id'] = $value->employee_id;
                        $tempArray['fullName'] = $value->fullName;
                        $tempArray['department_name'] = $value->department_name;
                        $tempArray['finger_print_id'] = $value->finger_print_id;
                        $tempArray['date'] = $data;
                        $tempArray['working_time'] = '';
                        $tempArray['in_time'] = '';
                        $tempArray['out_time'] = '';
                        $tempArray['lateCountTime'] = '';
                        $tempArray['ifLate'] = '';
                        $tempArray['totalLateTime'] = '';
                        $tempArray['workingHour'] = '';
                        if (in_array($data, $employeeLeaveRecords)) {
                            $tempArray['action'] = 'Leave';
                        } else {
                            $tempArray['action'] = 'Absence';
                        }
                        $dataFormat[] = $tempArray;
                    } else {
                        $tempArray['employee_id'] = $value->employee_id;
                        $tempArray['fullName'] = $value->fullName;
                        $tempArray['department_name'] = $value->department_name;
                        $tempArray['finger_print_id'] = $value->finger_print_id;
                        $tempArray['date'] = $value->date;
                        $tempArray['working_time'] = $value->working_time;
                        $tempArray['in_time'] = $value->in_time;
                        $tempArray['out_time'] = $value->out_time;
                        $tempArray['lateCountTime'] = $value->lateCountTime;
                        $tempArray['ifLate'] = $value->ifLate;
                        $tempArray['totalLateTime'] = $value->totalLateTime;
                        $tempArray['workingHour'] = $value->workingHour;
                        $tempArray['action'] = '';
                        $dataFormat[] = $tempArray;
                    }
                }
            }

            return $dataFormat;
        } catch (\Throwable $th) {
            return ['status' => false, 'error' => $th];
        }
    }

    public function number_of_working_days_date($from_date, $to_date)
    {
        $holidays = DB::select(DB::raw('call SP_getHoliday("' . $from_date . '","' . $to_date . '")'));
        $public_holidays = [];
        foreach ($holidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $public_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday()'));
        $weeklyHolidayArray = [];
        foreach ($weeklyHolidays as $weeklyHoliday) {
            $weeklyHolidayArray[] = $weeklyHoliday->day_name;
        }

        $target = strtotime($from_date);
        $workingDate = [];

        while ($target <= strtotime(date("Y-m-d", strtotime($to_date)))) {
            //get weekly  holiday name
            $timestamp = strtotime(date('Y-m-d', $target));
            $dayName = date("l", $timestamp);

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
            $end_date = $value->application_to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $leaveRecord[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }
        return $leaveRecord;
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

    public function ifEmployeeWasLeave($leave, $employee_id, $date)
    {
        $leaveRecord = [];
        $temp = [];
        foreach ($leave as $value) {
            if ($employee_id == $value->employee_id) {
                $start_date = $value->application_from_date;
                $end_date = $value->application_to_date;
                while (strtotime($start_date) <= strtotime($end_date)) {
                    $temp['employee_id'] = $employee_id;
                    $temp['date'] = $start_date;
                    $temp['leave_type_name'] = $value->leave_type_name;
                    $leaveRecord[] = $temp;
                    $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
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
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $govt_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        foreach ($govt_holidays as $val) {
            if ($val == $date) {
                return true;
            }
        }

        $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday()'));
        $timestamp = strtotime($date);
        $dayName = date("l", $timestamp);
        foreach ($weeklyHolidays as $v) {
            if ($v->day_name == $dayName) {
                return true;
            }
        }

        return false;
    }

    public function getDistanceBetweenPoints($latlngOfCompany, $latlngOfCurrentLocation)
    {
        $data = [];
        $lat1 = $latlngOfCompany['lat'];
        $lon1 = $latlngOfCompany['lng'];
        $lat2 = $latlngOfCurrentLocation['lat'];
        $lon2 = $latlngOfCurrentLocation['lng'];
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;

        $data['meters'] = $meters;
        $data['kilometers'] = $kilometers;
        $data['miles'] = $miles;
        return $data;
    }

    public function distance($latlngOfCompany, $latlngOfCurrentLocation, $unit)
    {

        $lat1 = $latlngOfCompany['lat'];
        $lon1 = $latlngOfCompany['lng'];
        $lat2 = $latlngOfCurrentLocation['lat'];
        $lon2 = $latlngOfCurrentLocation['lng'];

        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else if ($unit == "M") {
                return ($miles * 1.609344 * 1000);
            } else {
                return $miles;
            }
        }
    }
}
