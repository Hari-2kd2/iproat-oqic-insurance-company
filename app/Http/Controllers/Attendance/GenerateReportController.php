<?php

namespace App\Http\Controllers\Attendance;

use DateTime;
use Carbon\Carbon;
use App\Jobs\ReportJob;
use App\Model\Employee;
use App\Model\WorkShift;
use Carbon\CarbonPeriod;
use App\Model\Department;
use App\Model\EmployeeShift;
use Illuminate\Http\Request;
use App\Model\EmployeeInOutData;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\UserStatus;
use App\Lib\Enumerations\GeneralStatus;
use App\Lib\Enumerations\ShiftConstant;
use App\Lib\Enumerations\OvertimeStatus;
use App\Lib\Enumerations\EarlyLateStatus;
use App\Lib\Enumerations\AttendanceStatus;

class GenerateReportController extends Controller
{

    public function calculateAttendance(Request $request)
    {
        $branch_id = $request->branch_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $department_id = $request->department_id;
        $departmentList = Department::all();

        return view('admin.attendance.calculateAttendance.index', compact('from_date', 'to_date', 'branch_id', 'departmentList', 'department_id'));
    }

    public function generateManualAttendanceReport($finger_print_id, $date, $in_time = '', $out_time = '', $manual, $recompute)
    {
        ob_start();
        set_time_limit(0);
        info('Generate Manual Attendance Report.....................');
        $employee = Employee::status(UserStatus::$ACTIVE)->where('finger_id', $finger_print_id)->select('finger_id', 'employee_id')->first();
        ob_end_flush();

        return $this->calculate_attendance($employee->finger_id, $employee->employee_id, $date, $in_time, $out_time, $manual, $recompute);
        // return dispatch(new ReportJob($employee->finger_id, $employee->employee_id, $date, $in_time, $out_time, $manual, $recompute));

    }

    public function regenerateAttendanceReport(Request $request)
    {

        try {

            ob_start();
            set_time_limit(0);
            ini_set('memory_limit', '3072M');
            info('Calculate Attendance Report via recompute method.....................');

            $time_start = microtime(true);

            $datePeriod = CarbonPeriod::create(dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date));

            $employeeList = Employee::select('finger_id', 'employee_id')->status(UserStatus::$ACTIVE);

            if ($request->department_id != '') {
                $employeeList = $employeeList->whereIn('department_id', $request->department_id);
            }
            if ($request->branch_id != '') {
                $employeeList = $employeeList->whereIn('branch_id', $request->department_id);
            }

            $employeeList->chunk(5, function ($employeeData) use ($datePeriod) {
                foreach ($employeeData as $key => $employee) {
                    foreach ($datePeriod as $date) {
                        $date = $date->format('Y-m-d');
                        $in_time = '';
                        $out_time = '';
                        $manualAttendance = false;
                        $recompute = true;
                        // dispatch(new ReportJob($employee->finger_id, $employee->employee_id, $date, $in_time, $out_time, $manualAttendance, $recompute));
                        $this->calculate_attendance($employee->finger_id, $employee->employee_id, dateConvertFormtoDB($date), $in_time, $out_time, $manualAttendance, $recompute);
                    }
                }
            });

            $bug = 0;

            $time_end = microtime(true);
            $execution_time_in_seconds = ($time_end - $time_start) . ' Seconds';

            info('Execution_time_in_seconds : ' . $execution_time_in_seconds);
            ob_end_flush();
            echo 'success';
            // return redirect()->back()->with('success', 'Reports calculated Successfully');

        } catch (\Throwable $th) {
            $bug = $th->getMessage();
            info($bug);
            ob_end_flush();
            echo 'error';
            // return redirect()->back()->with('error', 'Something went wrong, Please try again!' . $bug);
        }
    }

    public function generateAttendanceReportForAnEmployee($finger_id, $date)
    {

        $employee = Employee::status(UserStatus::$ACTIVE)->where('finger_id', $finger_id)->select('finger_id', 'employee_id')->first();

        $in_time = '';
        $out_time = '';
        $manualAttendance = false;
        $recompute = true;

        dispatch(new ReportJob($employee->finger_id, $employee->employee_id, $date, $in_time, $out_time, $manualAttendance, $recompute));
    }

    public function generateAttendanceReport($date, $id = null)
    {
        \ob_start();
        \set_time_limit(0);
        info('Generate Attendance Report Scheduler.....................');

        $qry = '1 ';
        if ($id != null) {
            $qry .= ' AND finger_id=' . $id;
        }

        $employeeData = Employee::status(UserStatus::$ACTIVE)->whereRaw($qry)->select('finger_id', 'employee_id')->get();
        $in_time = '';
        $out_time = '';
        $manualAttendance = false;
        $recompute = true;

        foreach ($employeeData as $key => $employee) {
            // dispatch(new ReportJob($employee->finger_id, $employee->employee_id, $date, $in_time, $out_time, $manualAttendance, $recompute));
            $this->calculate_attendance($employee->finger_id, $employee->employee_id, $date, $in_time, $out_time, $manualAttendance, $recompute);
        }

        ob_end_flush();
    }

    public function store($data_format, $employee_id, $manualAttendance, $recompute)
    {
        //insert employee attendance data to report table
        $if_exists = EmployeeInOutData::where('finger_print_id', $data_format['finger_print_id'])->where('date', $data_format['date'])->first();
        $if_manual_override_exists = EmployeeInOutData::where('finger_print_id', $data_format['finger_print_id'])->where('date', $data_format['date'])->where('device_name', 'Manual')->first();

        if (($recompute && !$if_manual_override_exists) || ($recompute == false && $manualAttendance)) {
            if ($data_format != []) {
                if (!$if_exists) {
                    EmployeeInOutData::insert($data_format);
                    return true;
                } else {
                    unset($data_format['created_by']);
                    unset($data_format['created_at']);
                    $if_exists->update($data_format);
                    $if_exists->save();
                    return true;
                }
            } else {

                $tempArray = [];

                $govtHolidays = DB::select(DB::raw('call SP_getHoliday("' . $data_format['date'] . '","' . $data_format['date'] . '")'));
                $companyHolidayDetails = DB::select(DB::raw('call SP_getCompanyHoliday("' . $data_format['date'] . '","' . $data_format['date'] . '","' . $employee_id . '")'));

                if ($data_format['date'] > date("Y-m-d")) {

                    $tempArray['attendance_status'] = AttendanceStatus::$FUTURE;
                } else {

                    $ifHoliday = $this->ifHoliday($govtHolidays, $data_format['date']);
                    $ifCompanyHoliday = $this->ifCompanyHoliday($companyHolidayDetails, $data_format['date']);

                    if ($ifHoliday) {
                        $tempArray['attendance_status'] = AttendanceStatus::$HOLIDAY;
                    } elseif ($ifCompanyHoliday) {
                        $tempArray['attendance_status'] = AttendanceStatus::$HOLIDAY;
                    } else {
                        $tempArray['attendance_status'] = AttendanceStatus::$ABSENT;
                    }
                }

                if (!$if_exists) {
                    $data_format['attendance_status'] = $tempArray['attendance_status'];
                    EmployeeInOutData::insert($data_format);
                } else {
                    $data_format['attendance_status'] = $tempArray['attendance_status'];
                    $if_exists->update($data_format);
                    $if_exists->save();
                }
            }
        } else {
            info('Manual override skipped when calculating reports for an employee - ' . $data_format['finger_print_id'] . ' on ' . $data_format['date'] . '...........');
        }
    }

    public function calculate_attendance($finger_id, $employee_id, $date, $in_time = '', $out_time = '', $manualAttendance = false, $recompute = false)
    {

        $month = date('Y-m', strtotime($date));
        $dataSet = [];

        $day = 'd_' . (int) date('d', strtotime($date));

        $shift = EmployeeShift::where('finger_print_id', $finger_id)->where('month', $month)->first();

        if ($manualAttendance) {
            if ($shift && $shift->$day != null) {
                info('manualAttenadance With Shift Allotted' . $finger_id);
                $dataSet = $this->manualAttendanceReport($in_time, $out_time, $date, $finger_id, $shift, $day);
            } else {
                info('manualAttenadance ' . $finger_id);
                $dataSet = $this->manualAttendanceReport($in_time, $out_time, $date, $finger_id);
            }
        } else {

            if ($shift && $shift->$day != null) {
                info('shiftBasedReport ' . $finger_id);
                $dataSet = $this->shiftBasedReport($shift, $date, $month, $day, $finger_id);
            } else {

                info('autoGenReport ' . $finger_id);
                // return;
                $hasReport = EmployeeInOutData::where('finger_print_id', $finger_id)->whereDate('date', $date)->first();

                $start_time = WorkShift::orderBy('start_time', 'ASC')->first()->start_time;
                $minTime = date('Y-m-d H:i:s', strtotime('-' . ShiftConstant::$SHIFT_BUFFER_INT . ' minutes', strtotime($start_time)));

                $start_date = DATE('Y-m-d', strtotime($date)) . " " . date('H:i:s', strtotime('-' . ShiftConstant::$SHIFT_BUFFER_INT . ' minutes', strtotime($minTime)));
                $end_date = DATE('Y-m-d', strtotime($date . " +1 day")) . " 00:00:00";

                $fingerID = (object) ['finger_id' => $finger_id];

                $dataSet = $this->autoGenReport($start_date, $end_date, $fingerID, $hasReport ? true : false);
            }
        }

        return $this->store($dataSet, $employee_id, $manualAttendance, $recompute);
    }

    public function autoGenReport($date_from, $date_to, $finger_id, $reRun)
    {

        \set_time_limit(0);
        $results = [];
        $dataSet = [];
        $attendance_data = [];

        if ($reRun) {
            $results = DB::table('ms_sql')
                ->whereRaw("datetime >= '" . $date_from . "' AND datetime <= '" . $date_to . "'")
                ->where('ID', $finger_id->finger_id)
                ->orderby('datetime', 'ASC')
                ->get();
        } else {
            $results = DB::table('ms_sql')
                ->whereRaw("datetime >= '" . $date_from . "' AND datetime <= '" . $date_to . "'")
                ->where('ID', $finger_id->finger_id)
                ->where('status', '!=', null)
                ->orderby('datetime', 'ASC')
                ->get();
        }

        if (count($results) == 0) {

            $attendance_data['date'] = date('Y-m-d', strtotime($date_from));
            $attendance_data['finger_print_id'] = $finger_id->finger_id;
            $attendance_data['in_time'] = null;
            $attendance_data['out_time'] = null;
            $attendance_data['working_time'] = null;
            $attendance_data['working_hour'] = null;
            $attendance_data['device_name'] = null;
            $attendance_data['over_time'] = null;
            $attendance_data['status'] = 1;
            $attendance_data['attendance_status'] = AttendanceStatus::$ABSENT;
            $attendance_data['created_at'] = date('Y-m-d H:i:s');
            $attendance_data['updated_at'] = date('Y-m-d H:i:s');
            $attendance_data['created_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
            $attendance_data['updated_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
            $attendance_data['in_out_time'] = null;

            $dataSet = $attendance_data;
        } elseif (count($results) == 1) {

            $attendance_data['date'] = date('Y-m-d', strtotime($date_from));
            $attendance_data['finger_print_id'] = $finger_id->finger_id;
            $attendance_data['in_time'] = date('Y-m-d H:i:s', strtotime($results[0]->datetime));
            $attendance_data['out_time'] = null;
            $attendance_data['working_time'] = null;
            $attendance_data['working_hour'] = null;
            $attendance_data['over_time'] = null;
            $attendance_data['device_name'] = $results[0]->device_name;
            $attendance_data['status'] = 1;
            $attendance_data['attendance_status'] = AttendanceStatus::$ONETIMEINPUNCH;
            $attendance_data['created_at'] = date('Y-m-d H:i:s');
            $attendance_data['updated_at'] = date('Y-m-d H:i:s');
            $attendance_data['created_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
            $attendance_data['updated_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
            $attendance_data['in_out_time'] = date('d/m/y H:i', strtotime($results[0]->datetime)) . ":" . ('IN');

            $dataSet = $this->overtimeLateEarlyCalc($attendance_data);
        } elseif (count($results) >= 2) {

            $attendance_data['date'] = date('Y-m-d', strtotime($date_from));
            $attendance_data['finger_print_id'] = $finger_id->finger_id;
            $attendance_data['in_time'] = date('Y-m-d H:i:s', strtotime($results[0]->datetime));
            $attendance_data['out_time'] = date('Y-m-d H:i:s', strtotime($results[count($results) - 1]->datetime));
            $attendance_data['working_time'] = $this->workingtime($results[0]->datetime, $results[count($results) - 1]->datetime);
            $attendance_data['working_hour'] = $this->workingtime($results[0]->datetime, $results[count($results) - 1]->datetime);
            $attendance_data['device_name'] = $results[0]->device_name;
            $attendance_data['status'] = 1;
            $attendance_data['attendance_status'] = null;
            $attendance_data['over_time'] = null;
            $attendance_data['created_at'] = date('Y-m-d H:i:s');
            $attendance_data['updated_at'] = date('Y-m-d H:i:s');
            $attendance_data['created_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
            $attendance_data['updated_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
            $attendance_data['in_out_time'] = $this->in_out_time($results);

            $dataSet = $this->overtimeLateEarlyCalc($attendance_data);
        }

        return $dataSet;
    }

    public function manualAttendanceReport($fdatetime, $tdatetime, $date, $finger_id, $shift = null, $day = null)
    {
        $attendance_data = [];
        $dataSet = [];
        $working_time = $this->workingtime($fdatetime, $tdatetime);

        if ($shift != null) {
            $shiftData = WorkShift::where('work_shift_id', $shift->$day)->first();
        }

        $rawData = [
            'date' => date('Y-m-d', strtotime($date)),
            'finger_print_id' => $finger_id,
            'in_time' => date('Y-m-d H:i:s', strtotime($fdatetime)),
            'out_time' => date('Y-m-d H:i:s', strtotime($tdatetime)),
            'shift_name' => $shift != null ? $shiftData->shift_name : null,
            'work_shift_id' => $shift != null ? $shiftData->work_shift_id : null,
            'working_time' => $working_time,
            'working_hour' => null,
            'device_name' => 'Manual',
            'over_time' => null,
            'attendance_status' => null,
            'in_out_time' => date('d/m/y H:i', strtotime($fdatetime)) . ":" . ('IN,') . ' ' . date('d/m/y H:i', strtotime($tdatetime)) . ":" . ('OUT'),
        ];

        $attendance_data = $this->reportDataFormat($rawData);

        $dataSet = $this->overtimeLateEarlyCalc($attendance_data);

        return $dataSet;
    }

    public function shiftBasedReport($shift, $date, $month, $day, $finger_id)
    {
        // info('Shift Based Report function.....................');

        $attendance_data = [];
        $dataSet = [];

        $dailyShiftData = WorkShift::where('work_shift_id', $shift->$day)->first();

        $shiftStartTime = $date . ' ' . $dailyShiftData->start_time;
        $shiftEndTime = $date . ' ' . $dailyShiftData->end_time;

        if ($dailyShiftData->start_time > $dailyShiftData->end_time) {
            $nature = 'Night';
            $fdatetime = date('Y-m-d H:i:s', strtotime('-1 hours', strtotime($shiftStartTime)));
            $tdatetime = date('Y-m-d H:i:s', strtotime('+1 days +4 hours', strtotime($shiftEndTime)));
        } else {
            $nature = 'Day';
            $fdatetime = date('Y-m-d H:i:s', strtotime('-1 hours', strtotime($shiftStartTime)));
            $tdatetime = date('Y-m-d H:i:s', strtotime('+4 hours', strtotime($shiftEndTime)));
        }

        $results = DB::table('ms_sql')->whereRaw("datetime >= '" . $fdatetime . "' AND datetime <= '" . $tdatetime . "'")
            ->where('ID', $finger_id)->get();

        if (count($results) == 1) {
            $inTime = DB::table('ms_sql')->whereRaw("datetime >= '" . $fdatetime . "' AND datetime <= '" . $tdatetime . "'")
                ->where('ID', $finger_id)->min('datetime');
        } else {
            $inTime = DB::table('ms_sql')->whereRaw("datetime >= '" . $fdatetime . "' AND datetime <= '" . $tdatetime . "'")
                ->where('ID', $finger_id)->min('datetime');
            $outTime = DB::table('ms_sql')->whereRaw("datetime >= '" . $fdatetime . "' AND datetime <= '" . $tdatetime . "'")
                ->where('ID', $finger_id)->max('datetime');
        }

        if ($inTime != null && isset($outTime)) {

            $working_time = $this->workingtime($inTime, $outTime);
            $hour = explode(':', $working_time);

            $rawData = [
                'date' => date('Y-m-d', strtotime($date)),
                'finger_print_id' => $finger_id,
                'in_time' => date('Y-m-d H:i:s', strtotime($inTime)),
                'out_time' => date('Y-m-d H:i:s', strtotime($outTime)),
                'shift_name' => shiftList()[$shift->$day],
                'work_shift_id' => $shift->$day,
                'working_time' => $working_time,
                'working_hour' => null,
                'device_name' => null,
                'over_time' => null,
                'attendance_status' => null,
                'in_out_time' => date('d/m/y H:i', strtotime($inTime)) . ":" . 'IN' . ', ' . date('d/m/y H:i', strtotime($outTime)) . ":" . 'OUT',
            ];

            $attendance_data = $this->reportDataFormat($rawData);
            // $dataSet = $this->overtimeLateEarlyCalc($attendance_data, $shift->$day);
            $dataSet = $this->overtimeLateEarlyCalc($attendance_data);
        } elseif ($inTime != null) {

            $rawData = [
                'date' => date('Y-m-d', strtotime($date)),
                'finger_print_id' => $finger_id,
                'in_time' => date('Y-m-d H:i:s', strtotime($inTime)),
                'out_time' => null,
                'shift_name' => shiftList()[$shift->$day],
                'work_shift_id' => $shift->$day,
                'working_time' => null,
                'working_hour' => null,
                'device_name' => null,
                'over_time' => null,
                'attendance_status' => AttendanceStatus::$ONETIMEINPUNCH,
                'in_out_time' => date('d/m/y H:i', strtotime($inTime)) . ":" . 'IN',
            ];

            $dataSet = $this->reportDataFormat($rawData);
        } else {

            $rawData = [
                'date' => date('Y-m-d', strtotime($date)),
                'finger_print_id' => $finger_id,
                'in_time' => null,
                'out_time' => null,
                'shift_name' => shiftList()[$shift->$day],
                'work_shift_id' => $shift->$day,
                'working_time' => null,
                'working_hour' => null,
                'device_name' => null,
                'over_time' => null,
                'attendance_status' => AttendanceStatus::$ABSENT,
                'in_out_time' => null,
            ];

            $dataSet = $this->reportDataFormat($rawData);
        }

        return $dataSet;
    }

    public function reportDataFormat($data)
    {
        $attendance_data = [];
        $dataSet = [];

        $attendance_data['date'] = $data['date'];
        $attendance_data['finger_print_id'] = $data['finger_print_id'];
        $attendance_data['in_time'] = $data['in_time'];
        $attendance_data['out_time'] = $data['out_time'];
        $attendance_data['shift_name'] = $data['shift_name'];
        $attendance_data['work_shift_id'] = $data['work_shift_id'];
        $attendance_data['working_time'] = $data['working_time'];
        $attendance_data['working_hour'] = $data['working_hour'];
        $attendance_data['device_name'] = $data['device_name'];
        $attendance_data['over_time'] = isset($data['over_time']) ? $data['over_time'] : null;
        $attendance_data['in_out_time'] = $data['in_out_time'];
        $attendance_data['attendance_status'] = $data['attendance_status'];
        $attendance_data['early_by'] = isset($data['early_by']) ? $data['early_by'] : null;
        $attendance_data['late_by'] = isset($data['late_by']) ? $data['late_by'] : null;
        $attendance_data['status'] = GeneralStatus::$OKEY;
        $attendance_data['created_at'] = date('Y-m-d H:i:s');
        $attendance_data['updated_at'] = date('Y-m-d H:i:s');
        $attendance_data['created_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;
        $attendance_data['updated_by'] = isset(auth()->user()->user_id) ? auth()->user()->user_id : null;

        $dataSet = $attendance_data;

        return $dataSet;
    }

    public function overtimeLateEarlyCalc($data_format)
    {
        // info($data_format);
        $dataSet = [];
        $tempArray = [];

        // if ($data_format != [] && isset($data_format['working_time']) && $data_format['working_time'] != null) {
        if ($data_format != []) {

            // find employee early or late time and shift name
            if (isset($data_format['work_shift_id']) && $data_format['work_shift_id'] != null) {

                $shift_list = WorkShift::where('work_shift_id', $data_format['work_shift_id'])->first();

                $login_time = date('H:i:s', \strtotime($data_format['in_time']));
                $in_datetime = new DateTime($data_format['in_time']);
                $start_datetime = new DateTime($data_format['date'] . ' ' . $shift_list->start_time);
                $late_count_time = date('H:i', strtotime($shift_list->late_count_time));
                if ($in_datetime >= $start_datetime) {

                    $interval = $in_datetime->diff($start_datetime);
                    $tempArray['finger_print_id'] = $data_format['finger_print_id'];
                    $tempArray['work_shift_id'] = $shift_list->work_shift_id;
                    $tempArray['shift_name'] = $shift_list->shift_name;
                    $tempArray['start_time'] = $shift_list->start_time;
                    $tempArray['end_time'] = $shift_list->end_time;
                    $tempArray['early_by'] = null;
                    $tempArray['late_by'] = $interval->format('%H') . ":" . $interval->format('%I') . ":" . $interval->format('%S');
                    $tempArray['earlylate_in_status'] = EarlyLateStatus::$LATE_IN;
                } elseif ($in_datetime <= $start_datetime) {

                    $interval = $start_datetime->diff($in_datetime);
                    $tempArray['finger_print_id'] = $data_format['finger_print_id'];
                    $tempArray['work_shift_id'] = $shift_list->work_shift_id;
                    $tempArray['shift_name'] = $shift_list->shift_name;
                    $tempArray['start_time'] = $shift_list->start_time;
                    $tempArray['end_time'] = $shift_list->end_time;
                    $tempArray['early_by'] = $interval->format('%H') . ":" . $interval->format('%I') . ":" . $interval->format('%S');
                    $tempArray['late_by'] = null;
                    $tempArray['earlylate_in_status'] = EarlyLateStatus::$EARLY_IN;
                }
            } else {

                $shift_list = WorkShift::orderBy('start_time', 'ASC')->get();

                if (isset($data_format['in_time']) && $data_format['in_time'] != null) {
                    // if (isset($data_format['in_time']) && $data_format['in_time'] != null && isset($data_format['out_time']) && $data_format['out_time'] != null) {

                    $tempArray['finger_print_id'] = $data_format['finger_print_id'];
                    $tempArray['work_shift_id'] = null;
                    $tempArray['shift_name'] = null;
                    $tempArray['start_time'] = null;
                    $tempArray['end_time'] = null;
                    $tempArray['early_by'] = null;
                    $tempArray['late_by'] = null;
                    $tempArray['early_out'] = null;
                    $tempArray['late_out'] = null;

                    $interval = ShiftConstant::$SHIFT_BUFFER_INT;

                    while ($tempArray['work_shift_id'] == null) {

                        foreach ($shift_list as $key => $value) {

                            $in_time = new DateTime($data_format['in_time']);
                            $out_time = new DateTime($data_format['out_time']);
                            $login_time = date('H:i:s', \strtotime($data_format['in_time']));
                            $start_time = new DateTime($data_format['date'] . ' ' . $value->start_time);
                            $end_time = new DateTime($data_format['date'] . ' ' . $value->end_time);
                            $late_count_time = new DateTime($data_format['date'] . ' ' . $value->late_count_time);
                            $late_time = $late_count_time->diff($start_time);
                            $late_time = $late_time->format('%H') . ":" . $late_time->format('%I') . ":" . $late_time->format('%S');

                            $buffer_start_time = Carbon::createFromFormat('H:i:s', $value->start_time)->subMinutes($interval)->format('H:i:s');
                            $buffer_end_time = Carbon::createFromFormat('H:i:s', $value->start_time)->addMinutes($interval)->format('H:i:s');

                            $emp_shift = $this->shift_timing_array($login_time, $buffer_start_time, $buffer_end_time);

                            if ($emp_shift == \true) {

                                if ($in_time >= $start_time) {
                                    $interval = $in_time->diff($start_time);
                                    $tempArray['finger_print_id'] = $data_format['finger_print_id'];
                                    $tempArray['work_shift_id'] = $value->work_shift_id;
                                    $tempArray['shift_name'] = $value->shift_name;
                                    $tempArray['start_time'] = $value->start_time;
                                    $tempArray['end_time'] = $value->end_time;
                                    $late_by = $interval->format('%H') . ":" . $interval->format('%I') . ":" . $interval->format('%S');
                                    $tempArray['late_by'] = strtotime($late_by) > strtotime($late_time) ? $late_by : null;
                                    $tempArray['earlylate_in_status'] = strtotime($late_by) > strtotime($late_time) ? EarlyLateStatus::$LATE_IN : null;
                                } elseif ($in_time <= $start_time) {
                                    $interval = $start_time->diff($in_time);
                                    $tempArray['finger_print_id'] = $data_format['finger_print_id'];
                                    $tempArray['work_shift_id'] = $value->work_shift_id;
                                    $tempArray['shift_name'] = $value->shift_name;
                                    $tempArray['start_time'] = $value->start_time;
                                    $tempArray['end_time'] = $value->end_time;
                                    $early_by = $interval->format('%H') . ":" . $interval->format('%I') . ":" . $interval->format('%S');
                                    $tempArray['early_by'] = strtotime($early_by) > strtotime($late_time) ? $early_by : null;
                                    $tempArray['earlylate_in_status'] = strtotime($early_by) > strtotime($late_time) ? EarlyLateStatus::$EARLY_IN : null;
                                }

                                if ($data_format['out_time'] && $out_time >= $end_time) {
                                    $intervalOut = $out_time->diff($end_time);
                                    $late_out = $intervalOut->format('%H') . ":" . $intervalOut->format('%I') . ":" . $intervalOut->format('%S');
                                    $tempArray['late_out'] = strtotime($late_out) > strtotime($late_time) ? $late_out : null;
                                    $tempArray['earlylate_out_status'] =  EarlyLateStatus::$LATE_OUT;
                                } elseif ($data_format['out_time'] && $out_time <= $end_time) {
                                    $intervalOut = $end_time->diff($out_time);
                                    $early_out = $intervalOut->format('%H') . ":" . $intervalOut->format('%I') . ":" . $intervalOut->format('%S');
                                    $tempArray['early_out'] = strtotime($early_out) > strtotime($late_time) ? $early_out : null;
                                    $tempArray['earlylate_out_status'] =  EarlyLateStatus::$EARLY_OUT;
                                }

                                break;
                            } else {
                                $interval += 30;
                            }
                        }
                    }
                }
            }

            // dd([$data_format, $tempArray]);

            // find employee over time
            if (isset($tempArray['work_shift_id']) && $tempArray['work_shift_id'] != null && isset($data_format['working_time']) && $data_format['working_time'] != null) {
                // if (isset($tempArray['work_shift_id']) && $tempArray['work_shift_id'] != null) {

                $shiftStartTime = new DateTime(date('H:i:s', strtotime($tempArray['start_time'])));
                $shiftEndTime = new DateTime(date('H:i:s', strtotime($tempArray['end_time'])));
                $shiftEndTimeForAtt = new DateTime(date('H:i:s', strtotime('-5 minutes', strtotime($tempArray['end_time']))));
                $shiftEndTimeForAtt = date('H:i:s', strtotime('-5 minutes', strtotime($tempArray['end_time'])));

                if ($shiftStartTime < $shiftEndTime) {
                    $employeeOutTime = new DateTime(date('H:i:s', strtotime($data_format['out_time'])));
                } else {
                    $endDate = date('Y-m-d H:i:s', strtotime('+1 days', strtotime($data_format['date'] . ' ' . $tempArray['end_time'])));
                    $shiftEndTime = new DateTime(date('Y-m-d H:i:s', strtotime($endDate)));
                    $employeeOutTime = new DateTime($data_format['out_time']);
                    $employeeOutTime = date('H:i:s', strtotime($data_format['out_time']));
                }

                if ($employeeOutTime >= $shiftEndTimeForAtt) {
                    $tempArray['attendance_status'] = AttendanceStatus::$PRESENT;
                    // $tempArray['earlylate_out_status'] = EarlyLateStatus::$LATE_OUT;
                } else {
                    $tempArray['attendance_status'] = AttendanceStatus::$LESSHOURS;
                    // $tempArray['earlylate_out_status'] = EarlyLateStatus::$EARLY_OUT;
                }

                if ($employeeOutTime > $shiftEndTime) {

                    $over_time = $shiftEndTime->diff($employeeOutTime);
                    $tempArray['over_time'] = $this->check_overtime($over_time);
                } else {
                    $tempArray['over_time'] = null;
                }
            } else if (!isset($tempArray['work_shift_id']) || $tempArray['work_shift_id'] == null) {

                $workingTime = new DateTime($data_format['working_time']);
                $naShiftDuration = new DateTime(ShiftConstant::$NA_OVERTIME_HOUR_TIME);

                if ($workingTime >= $naShiftDuration) {
                    $tempArray['attendance_status'] = AttendanceStatus::$PRESENT;
                } else if ($workingTime < $naShiftDuration) {
                    $tempArray['attendance_status'] = AttendanceStatus::$LESSHOURS;
                }

                if ($workingTime > $naShiftDuration) {
                    $over_time = $naShiftDuration->diff($workingTime);
                    $tempArray['over_time'] = $this->check_overtime($over_time);
                } else {
                    $tempArray['over_time'] = null;
                }
            }

            $dataSet = array_merge($data_format, $tempArray);

            unset($dataSet['start_time']);
            unset($dataSet['end_time']);
            return $dataSet;
        }
    }

    public function check_overtime($over_time)
    {
        $roundMinutes = (int) $over_time->i >= OvertimeStatus::$OT_MIN_START_INT ? OvertimeStatus::$OT_MIN_START_INT : '00';
        $roundHours = (int) $over_time->h >= OvertimeStatus::$OT_HOUR_START_INT ? sprintf("%02d", ($over_time->h)) : '00';

        if ($over_time->h >= OvertimeStatus::$OT_HOUR_START_INT) {
            $overtime = $roundHours . ':' . $roundMinutes;
        } else {
            $overtime = null;
        }

        return $overtime;
    }

    public function over_time($working_time, $shift_time)
    {
        $workingTime = new DateTime($working_time);
        $actualTime = new DateTime($shift_time);
        $overTime = null;

        if ($workingTime > $actualTime) {
            $over_time = $actualTime->diff($workingTime);
            $roundMinutes = (int) $over_time->i >= 30 ? '30' : '00';
            $roundHours = (int) $over_time->h >= 1 ? sprintf("%02d", ($over_time->h)) : '00';

            if ($over_time->h >= 1) {
                $overTime = $roundHours . ':' . $roundMinutes;
            }
        }

        return $overTime;
    }

    public function in_out_time($array)
    {
        $result = [];
        $count = count($array);

        foreach ($array as $key => $value) {
            if ($key == 0) {
                $result[] = date('d/m/y H:i', strtotime($value->datetime)) . ':' . 'IN';
            } elseif ($key == ($count - 1)) {
                $result[] = date('d/m/y H:i', strtotime($value->datetime)) . ':' . 'OUT';
            } else {
                $result[] = date('d/m/y H:i', strtotime($value->datetime)) . ':' . 'BTW';
            }
        }

        $str = json_encode($result);
        $str = str_replace('[', '', $str);
        $str = str_replace(']', '', $str);
        $str = str_replace('"', '', $str);
        $str = str_replace("\/", '/', $str);

        return $str;
    }

    public function calculate_hours_mins($datetime1, $datetime2)
    {
        $interval = $datetime1->diff($datetime2);
        return $interval->format('%h') . ":" . $interval->format('%i') . ":" . $interval->format('%s');
    }

    public function calculate_total_working_hours($at)
    {
        $total_seconds = 0;
        for ($i = 0; $i < count($at); $i++) {
            $seconds = 0;
            $timestr = $at[$i]['subtotalhours'];

            $parts = explode(':', $timestr);

            $seconds = ($parts[0] * 60 * 60) + ($parts[1] * 60) + $parts[2];
            $total_seconds += $seconds;
        }
        return gmdate("H:i:s", $total_seconds);
    }

    public function find_closest_time($dates, $first_in)
    {

        function closest($dates, $findate)
        {
            $newDates = array();

            foreach ($dates as $date) {
                $newDates[] = strtotime($date);
            }

            sort($newDates);

            foreach ($newDates as $a) {
                if ($a >= strtotime($findate)) {
                    return $a;
                }
            }
            return end($newDates);
        }

        $values = closest($dates, date('Y-m-d H:i:s', \strtotime($first_in)));
    }

    public function shift_timing_array($in_time, $start_shift, $end_shift)
    {
        $shift_status = $in_time <= $end_shift && $in_time >= $start_shift;
        return $shift_status;
    }

    public function workingtime($from, $to)
    {
        $date1 = new DateTime($to);
        $date2 = $date1->diff(new DateTime($from));
        $hours = ($date2->days * 24);
        $hours = $hours + $date2->h;

        return $hours . ":" . sprintf('%02d', $date2->i) . ":" . sprintf('%02d', $date2->s);
    }

    public function ifCompanyHoliday($compHolidays, $date)
    {

        $comp_holidays = [];
        foreach ($compHolidays as $holidays) {
            $start_date = $holidays->fdate;
            $end_date = $holidays->tdate;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $comp_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        foreach ($comp_holidays as $val) {
            if ($val == $date) {
                return true;
            }
        }

        return false;
    }

    public function ifHoliday($govtHolidays, $date)
    {
        $ph = [];

        foreach ($govtHolidays as $holidays) {
            $start_date = $holidays->from_date;
            $end_date = $holidays->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $ph[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        foreach ($ph as $val) {
            if ($val == $date) {
                return true;
            }
        }
        return false;
    }
}
