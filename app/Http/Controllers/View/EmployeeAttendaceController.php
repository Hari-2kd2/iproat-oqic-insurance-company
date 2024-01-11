<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\AttendanceStatus;
use App\Lib\Enumerations\LeaveStatus;
use App\Lib\Enumerations\UserStatus;
use App\Model\Employee;
use App\Model\LeaveApplication;
use App\Model\MsSql;
use App\Model\ViewEmployeeInOutData;
use App\Model\WorkShift;
use App\Repositories\AttendanceRepository;
use App\Repositories\LeaveRepository;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeAttendaceController extends Controller
{

    protected $leaveRepository;
    protected $attendanceRepository;

    public function __construct(LeaveRepository $leaveRepository, AttendanceRepository $attendanceRepository)
    {
        $this->leaveRepository = $leaveRepository;
        $this->attendanceRepository = $attendanceRepository;
    }

    public function fetchRaw()
    {
        \ob_start();
        \set_time_limit(0);
        $time_start = microtime(true);
        Log::info("Log Controller is working fine!");
        $lastLogRow = DB::table('ms_sql')->max('datetime');
        $date = Carbon::now()->subDay(0)->format('Y-m-d');
        $month = Carbon::parse($date)->format("m");
        $year = Carbon::parse($date)->format("_Y");
        $table_name = 'DeviceLogs_' . $month . $year;
        $insertData = [];
        $bug = \null;

        if ($lastLogRow) {
            $LogCollections = DB::connection('sqlsrv')->table($table_name)
                ->select('DeviceId', 'UserId', 'LogDate', 'Direction', 'C1', 'DeviceLogId')
                ->orderBy('LogDate', 'ASC')
                ->where('LogDate', '>=', $lastLogRow)
                ->select('DeviceId', 'UserId', 'LogDate', 'Direction', 'C1', 'DeviceLogId')
                ->get();
        } else {
            $LogCollections = DB::connection('sqlsrv')->table($table_name)
                ->select('DeviceId', 'UserId', 'LogDate', 'Direction', 'C1', 'DeviceLogId')
                ->orderBy('LogDate', 'ASC')
                ->select('DeviceId', 'UserId', 'LogDate', 'Direction', 'C1', 'DeviceLogId')
                ->get();
        }

        foreach ($LogCollections as $key => $log) {

            $check_record = DB::table('ms_sql')->where('ID', $log->UserId)->where('evtgluid', $log->DeviceLogId)->where('datetime', $log->LogDate)->first();

            if (!$check_record) {

                $insertData[] = [
                    'evtlguid' => $log->DeviceLogId,
                    'device' => $log->DeviceId,
                    'datetime' => $log->LogDate,
                    'punching_time' => Date('Y-m-d H:i:s'),
                    'ID' => $log->UserId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'type' => $log->C1,
                ];
            }
        }

        try {
            DB::beginTransaction();
            MsSql::insert($insertData);
            DB::commit();
            $bug = 0;
        } catch (\Throwable $e) {
            DB::rollback();
            echo "<pre>";
            print_r($e->getMessage());
            echo "</pre>";
        } finally {
            echo "<pre>";
            print_r($insertData);
            echo "</pre>";
        }

        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);

        echo "<br>";
        echo '<b>Total Execution Time:</b> ' . ($execution_time) . 'Seconds';
        echo '<b>Total Execution Time:</b> ' . ($execution_time * 1000) . 'Milliseconds';
        echo "<br>";

        ob_end_flush();

        $bug == 0 ? $status = array('status' => 'Success') : $status = array('status' => 'Failed');
        return $status;
    }

    public function attendance($finger_print_id = null, $manualAttendance = false, $manualDate = null, $dateList = null)
    {
        \ob_start();
        \set_time_limit(0);
        $time_start = microtime(true);
        $data_format = [];

        // for loop
        // for ($day = 10; $day <= 10; $day++) {

        // $day         = date('d', \strtotime('-1 days'));
        // $start = sprintf("%02d", $day);
        // $date = DATE('Y-m') . '-' . $start . '';

        $date = DATE('Y-m-d', strtotime('-1 days'));
        $dates = [];

        if ($dateList == null) {
            $dates[] = $date;
        } else {
            $dates = $dateList;
        }

        $reRun = false;
        $secondRun = false;

        if ($finger_print_id != null && $manualAttendance != false && $manualDate != null) {
            $reRun = true;
            $date = $manualDate;
            ViewEmployeeInOutData::where('finger_print_id', $finger_print_id)->where('date', dateConvertFormtoDB($date))->delete();
            $employees = Employee::where('status', UserStatus::$ACTIVE)->where('finger_id', $finger_print_id)->select('finger_id', 'employee_id')->get();
        } else {
            $employees = Employee::where('status', UserStatus::$ACTIVE)->select('finger_id', 'employee_id')->groupby('finger_id')->get();
        }

        foreach ($dates as $value) {

            $date = date('Y-m-d', strtotime($value));

            foreach ($employees as $finger_id) {

                $rework = ViewEmployeeInOutData::whereRaw("date= '" . $date . "' and finger_print_id= '" . $finger_id->finger_id . "'")->first();

                if ($rework || $reRun == true) {
                    $secondRun = true;
                }

                $start_date = DATE('Y-m-d', strtotime($date)) . " 05:30:00";
                $end_date = DATE('Y-m-d', strtotime($date . " +1 day")) . " 08:00:00";

                $data_format = $this->calculate_attendance($start_date, $end_date, $finger_id, $secondRun);

                $shift_list = WorkShift::all();

                //find employee over time
                if ($data_format != [] && isset($data_format['working_time'])) {

                    $workingTime = new DateTime($data_format['working_time']);
                    $actualTime = new DateTime('07:59:59');

                    if ($workingTime > $actualTime) {
                        $over_time = $actualTime->diff($workingTime);

                        if ($over_time->h >= 3) {
                            $data_format['attendance_status'] = AttendanceStatus::$PRESENT;
                            $data_format['over_time'] = (sprintf("%02d", ($over_time->h - 1)) . ':' . sprintf("%02d", $over_time->i));
                        } else {
                            $data_format['attendance_status'] = AttendanceStatus::$PRESENT;
                            $data_format['over_time'] = null;
                        }
                    } else {
                        $data_format['attendance_status'] = AttendanceStatus::$LESSHOURS;
                        $data_format['over_time'] = null;
                    }

                    // find employee early or late time
                    if ($data_format != [] && isset($data_format['in_time'])) {

                        foreach ($shift_list as $key => $value) {

                            $in_time = new DateTime($data_format['in_time']);
                            $login_time = date('H:i:s', \strtotime($data_format['in_time']));
                            $start_time = new DateTime($data_format['date'] . ' ' . $value->start_time);

                            $buffer_start_time = Carbon::createFromFormat('H:i:s', $value->start_time)->subMinutes(30)->format('H:i:s');
                            $buffer_end_time = Carbon::createFromFormat('H:i:s', $value->start_time)->addMinutes(30)->format('H:i:s');

                            $emp_shift = $this->shift_timing_array($login_time, $buffer_start_time, $buffer_end_time);

                            info($data_format['finger_print_id']);
                            info($emp_shift);

                            if ($emp_shift == \true) {

                                if ($in_time >= $start_time) {
                                    if (!isset($data_format['over_time'])) {
                                        $interval = $in_time->diff($start_time);
                                        $data_format['shift_name'] = $value->shift_name;
                                        $data_format['work_shift_id'] = $value->work_shift_id;
                                        $data_format['late_by'] = $interval->format('%H') . ":" . $interval->format('%I');
                                    }
                                } elseif ($in_time <= $start_time) {
                                    $interval = $start_time->diff($in_time);
                                    $data_format['shift_name'] = $value->shift_name;
                                    $data_format['work_shift_id'] = $value->work_shift_id;
                                    $data_format['early_by'] = $interval->format('%H') . ":" . $interval->format('%I');
                                }

                                break;
                            }
                        }

                    } else {
                        $data_format['early_by'] = null;
                        $data_format['late_by'] = null;
                    }
                }

                //insert employee attendacne data to report table
                if ($data_format != [] && (isset($data_format['working_time']) || isset($data_format['in_time']) || isset($data_format['out_time']))) {
                    $workingTime = explode(':', $data_format['working_time']);

                    if ($workingTime[0] >= 0) {
                        $if_exists = ViewEmployeeInOutData::where('finger_print_id', $data_format['finger_print_id'])->where('date', $data_format['date'])->first();

                        if (!$if_exists) {
                            ViewEmployeeInOutData::insert($data_format);
                        } else {
                            ViewEmployeeInOutData::where('date', $data_format['date'])->where('finger_print_id', $data_format['finger_print_id'])->update($data_format);
                        }
                    }
                } else {

                    $if_exists = ViewEmployeeInOutData::where('finger_print_id', $finger_id->finger_id)->where('date', date('Y-m-d', \strtotime($start_date)))->first();

                    $data_format = [
                        'date' => date('Y-m-d', \strtotime($start_date)),
                        'finger_print_id' => $finger_id->finger_id,
                        'in_time' => null,
                        'out_time' => null,
                        'working_time' => null,
                        'working_hour' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'status' => 1,
                    ];

                    $tempArray = [];

                    $govtHolidays = DB::select(DB::raw('call SP_getHoliday("' . $date . '","' . $date . '")'));

                    $leave = LeaveApplication::select('application_from_date', 'application_to_date', 'employee_id', 'leave_type_name')
                        ->join('leave_type', 'leave_type.leave_type_id', 'leave_application.leave_type_id')
                        ->where('status', LeaveStatus::$APPROVE)
                        ->where('application_from_date', '>=', $date)
                        ->where('application_to_date', '<=', $date)
                        ->get();

                    $hasLeave = $this->attendanceRepository->ifEmployeeWasLeave($leave, $finger_id->employee_id, $date);
                    if ($hasLeave) {
                        $tempArray['attendance_status'] = AttendanceStatus::$LEAVE;
                    } else {
                        if ($date > date("Y-m-d")) {
                            $tempArray['attendance_status'] = AttendanceStatus::$FUTURE;
                        } else {
                            $ifHoliday = $this->attendanceRepository->ifHoliday($govtHolidays, $date, $finger_id->employee_id);
                            if ($ifHoliday['weekly_holiday'] == true) {
                                $tempArray['attendance_status'] = AttendanceStatus::$HOLIDAY;
                            } elseif ($ifHoliday['govt_holiday'] == true) {
                                $tempArray['attendance_status'] = AttendanceStatus::$HOLIDAY;
                            } else {
                                $tempArray['attendance_status'] = AttendanceStatus::$ABSENT;
                            }
                        }
                    }
                    if (!$if_exists) {
                        $data_format['attendance_status'] = $tempArray['attendance_status'];
                        ViewEmployeeInOutData::insert($data_format);
                    } else {
                        $data_format['attendance_status'] = $tempArray['attendance_status'];
                        $if_exists->update($data_format);
                        $if_exists->save();
                    }
                }
            }

        }

        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);

        // echo '<br> <b>Total Execution Time:</b> ' . ($execution_time) . 'Seconds';
        // echo '<b>Total Execution Time:</b> ' . ($execution_time * 1000) . 'Milliseconds <br>';
        ob_end_flush();

        if ($finger_print_id != null && $manualAttendance != false && $manualDate != null) {
            return redirect('manualAttendance')->with('success', 'Attendance successfully saved.');
        }
    }

    public function calculate_attendance($date_from, $date_to, $finger_id, $reRun)
    {
        $k = 0;
        $a = 0;
        $first_row = 0;
        $at = [];
        $bt = [];
        $first_row_2 = 0;
        $at_id = [];
        $bt_id = [];
        $in_out_time_at = [];
        $in_out_time_bt = [];
        $attendance_data = [];
        $device_name_at = [];
        $device_name_bt = [];
        \set_time_limit(0);

        $deviceSerialNo = [
            'BRM9193360148',
            'BRM9193360137',
            'BRM9193360058',
            'BRM9193360034',
            'BRM9193360025',
            'BRM9192960031',
            'BRM9193360059',
            'BRM9191060473',
            'BRM9193360057',
            'BRM9193360055',
            'Manual',
            'Mobile',
        ];

        MsSql::whereRaw("datetime >= '" . $date_from . "' AND datetime <= '" . $date_to . "'")
            ->where('ID', $finger_id->finger_id)
            ->where('type', 1)
            ->update(['type' => 'IN']);
        MsSql::whereRaw("datetime >= '" . $date_from . "' AND datetime <= '" . $date_to . "'")
            ->where('ID', $finger_id->finger_id)
            ->where('type', 2)
            ->update(['type' => 'OUT']);

        $results = DB::table('ms_sql')
            ->whereRaw("datetime >= '" . $date_from . "' AND datetime <= '" . $date_to . "'")
            ->where('ID', $finger_id->finger_id)
        // ->whereIn('devuid', $deviceSerialNo)
            ->where('status', 0)
            ->orderby('datetime', 'ASC')
            ->get();

        if ($reRun) {
            $results = DB::table('ms_sql')
                ->whereRaw("datetime >= '" . $date_from . "' AND datetime <= '" . $date_to . "'")
                ->where('ID', $finger_id->finger_id)
            // ->whereIn('devuid', $deviceSerialNo)
                ->orderby('datetime', 'ASC')
                ->get();
        }

        if (count($results) == 1 && $results[0]->type == 'IN') {

            $attendance_data['date'] = date('Y-m-d', strtotime($results[0]->datetime));
            $attendance_data['in_time'] = date('Y-m-d H:i:s', strtotime($results[0]->datetime));
            $attendance_data['finger_print_id'] = $finger_id->finger_id;
            $attendance_data['out_time'] = \null;
            $attendance_data['working_time'] = \null;
            $attendance_data['status'] = 2;
            $attendance_data['device_name'] =$results[0]->device_name;
            $attendance_data['attendance_status'] = AttendanceStatus::$ONETIMEINPUNCH;
            $attendance_data['inout_status'] = $results[0]->inout_status;
            $attendance_data['created_at'] = date('Y-m-d H:i:s');
            $attendance_data['updated_at'] = date('Y-m-d H:i:s');
            $attendance_data['in_out_time'] = date('H:i', strtotime($results[0]->datetime)) . ":" . $results[0]->type . '(' . $results[0]->device_name . ')';
            // echo "<pre>";
            // print_r($attendance_data);
            // echo "</pre>";
            return $attendance_data;
        } elseif (count($results) == 1 && $results[0]->type == 'OUT') {

            $attendance_data['date'] = date('Y-m-d', strtotime($results[0]->datetime));
            $attendance_data['out_time'] = date('Y-m-d H:i:s', strtotime($results[0]->datetime));
            $attendance_data['finger_print_id'] = $finger_id->finger_id;
            $attendance_data['in_time'] = \null;
            $attendance_data['working_time'] = \null;
            $attendance_data['status'] = 2;
            $attendance_data['device_name'] =$results[0]->device_name;
            $attendance_data['attendance_status'] = AttendanceStatus::$ONETIMEOUTPUNCH;
            $attendance_data['inout_status'] = $results[0]->inout_status;
            $attendance_data['created_at'] = date('Y-m-d H:i:s');
            $attendance_data['updated_at'] = date('Y-m-d H:i:s');
            $attendance_data['in_out_time'] = date('H:i', strtotime($results[0]->datetime)) . ":" . $results[0]->type . '(' . $results[0]->device_name . ')';
            // echo "<pre>";
            // print_r($attendance_data);
            // echo "</pre>";
            return $attendance_data;
        } elseif (count($results) > 1) {

            $count_check = 0;
            $count_check_2 = 0;
            $count_check_3 = 0;

            for ($i = 0; $i < count($results); $i++) {
                if (strtolower($results[$i]->type) == 'IN') {
                    $count_check++;
                    array_push($primary_id, $results[$i]->primary_id);
                    array_push($in_out_time_record, (date('H:i', strtotime($results[$i]->datetime)) . ':' . strtolower($results[$i]->type) . '(' . $results[$i]->device_name . ')'));
                }
            }

            for ($i = 0; $i < count($results); $i++) {
                if (strtolower($results[$i]->type) == 'OUT') {
                    $count_check_2++;
                    array_push($primary_id_2, $results[$i]->primary_id);
                    array_push($in_out_time_record_2, (date('H:i', strtotime($results[$i]->datetime)) . ':' . strtolower($results[$i]->type) . '(' . $results[$i]->device_name . ')'));
                }
            }

            for ($i = 0; $i < count($results); $i++) {
                if (strtolower($results[0]->type) == 'OUT') {
                    $count_check_3++;
                    array_push($primary_id, $results[0]->primary_id);
                    array_push($in_out_time_record_3, (date('H:i', strtotime($results[$i]->datetime)) . ':' . strtolower($results[$i]->type) . '(' . $results[$i]->device_name . ')'));
                } elseif ($i != 0 && strtolower($results[$i]->type) == 'IN') {
                    $count_check_3++;
                    array_push($primary_id_3, $results[$i]->primary_id);
                    array_push($in_out_time_record_3, (date('H:i', strtotime($results[$i]->datetime)) . ':' . strtolower($results[$i]->type) . '(' . $results[$i]->device_name . ')'));
                }
            }

            if ($count_check == count($results)) {

                $attendance_data['date'] = date('Y-m-d', strtotime($results[0]->datetime));
                $attendance_data['in_time'] = date('Y-m-d H:i:s', strtotime($results[0]->datetime));
                $attendance_data['finger_print_id'] = $finger_id->finger_id;
                $attendance_data['out_time'] = \null;
                $attendance_data['working_time'] = \null;
                $attendance_data['status'] = 2;
                $attendance_data['device_name'] =$results[0]->device_name;
                $attendance_data['attendance_status'] = AttendanceStatus::$LESSHOURS;
                $attendance_data['inout_status'] = $results[0]->inout_status;
                $attendance_data['created_at'] = date('Y-m-d H:i:s');
                $attendance_data['updated_at'] = date('Y-m-d H:i:s');
                $attendance_data['in_out_time'] = $this->in_out_time_list($in_out_time_record);
                $update = DB::table('ms_sql')->whereIn('primary_id', $primary_id)->update(['status' => 1]);
                // echo "<pre>";
                // print_r($attendance_data);
                // echo "</pre>";
                return $attendance_data;
            } elseif ($count_check_2 == count($results)) {

                $attendance_data['date'] = date('Y-m-d', strtotime($results[0]->datetime));
                $attendance_data['out_time'] = date('Y-m-d H:i:s', strtotime($results[count($results) - 1]->datetime));
                $attendance_data['finger_print_id'] = $finger_id->finger_id;
                $attendance_data['in_time'] = \null;
                $attendance_data['working_time'] = \null;
                $attendance_data['status'] = 2;
                $attendance_data['device_name'] =$results[count($results) - 1]->device_name;
                $attendance_data['attendance_status'] = AttendanceStatus::$LESSHOURS;
                $attendance_data['inout_status'] = $results[0]->inout_status;
                $attendance_data['created_at'] = date('Y-m-d H:i:s');
                $attendance_data['updated_at'] = date('Y-m-d H:i:s');
                $attendance_data['in_out_time'] = $this->in_out_time_list($in_out_time_record_2);
                $update = DB::table('ms_sql')->whereIn('primary_id', $primary_id_2)->update(['status' => 1]);
                // echo "<pre>";
                // print_r($attendance_data);
                // echo "</pre>";
                return $attendance_data;
            } elseif ($count_check_3 == count($results)) {

                $attendance_data['date'] = date('Y-m-d', strtotime($results[0]->datetime));
                $attendance_data['in_time'] = date('Y-m-d H:i:s', strtotime($results[1]->datetime));
                $attendance_data['finger_print_id'] = $finger_id->finger_id;
                $attendance_data['out_time'] = \null;
                $attendance_data['working_time'] = \null;
                $attendance_data['status'] = 2;
                $attendance_data['device_name'] =$results[1]->device_name;
                $attendance_data['attendance_status'] = AttendanceStatus::$LESSHOURS;
                $attendance_data['inout_status'] = $results[0]->inout_status;
                $attendance_data['created_at'] = date('Y-m-d H:i:s');
                $attendance_data['updated_at'] = date('Y-m-d H:i:s');
                $attendance_data['in_out_time'] = $this->in_out_time_list($in_out_time_record_3);
                $update = DB::table('ms_sql')->whereIn('primary_id', $primary_id_3)->update(['status' => 1]);
                // echo "<pre>";
                // print_r($attendance_data);
                // echo "</pre>";
                return $attendance_data;
            }
        }

        // dd($results);

        foreach ($results as $key => $row) {

            $init = MsSql::where('primary_id', $row->primary_id)->update(['status' => 2]);

            $attendance_data['device_name'] = $row->device_name;
            $attendance_data['inout_status'] = $row->inout_status;

            if ($first_row == 0 && $row->type == "OUT") {
                array_push($at_id, $row->primary_id);
                array_push($in_out_time_at, $row->type);
                array_push($device_name_at, $row->device_name);
                //  echo 'at first row OUT <br>';
                continue;
            } elseif (!isset($at[$k]['fromdate']) && $row->type == "OUT" && $first_row_2 == 0) {
                $j = $k;
                $j--;
                if (!isset($at[$j]['fromdate'])) {
                    array_push($at_id, $row->primary_id);
                    array_push($in_out_time_at, $row->type);
                    array_push($device_name_at, $row->device_name);
                    //  echo 'at first row 2 OUT <br>';

                    continue;
                }
            } elseif (isset($at[$k]['fromdate']) && $row->type == "IN" && $first_row_2 == 0) {

                $datetime1 = new DateTime($at[$k]['fromdate']);
                $datetime2 = new DateTime($row->datetime);
                $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                $pieces = explode(":", $subtotal);
                if ($pieces[0] > 16) {

                    $bt[$a]['fromdate'] = $row->datetime;
                    $bt[$a]['statusin'] = $row->type;
                    array_push($bt_id, $row->primary_id);
                    array_push($in_out_time_bt, $row->type);
                    array_push($device_name_bt, $row->device_name);

                    $first_row_2 = 1;
                    // echo ' bt first_row_2 = 0  - >9 <br>';

                    continue;
                }

                array_push($at_id, $row->primary_id);
                array_push($in_out_time_at, $row->type);
                array_push($device_name_at, $row->device_name);
                // echo ' at first_row_2 = 0 - <9 <br>';

                continue;
            }

            if ($row->type == "IN") {
                $j = $k;
                $j--;
                if ($first_row_2 == 1) {
                    if (isset($bt[$a]['fromdate'])) {
                        array_push($at_id, $row->primary_id);
                        array_push($in_out_time_at, $row->type);
                        array_push($device_name_at, $row->device_name);
                        //  echo 'IN at first_row_2 = 1  <br>';

                        continue;
                    }
                    $bt[$a]['fromdate'] = $row->datetime;
                    $bt[$a]['statusin'] = $row->type;
                    array_push($bt_id, $row->primary_id);
                    array_push($in_out_time_bt, $row->type);
                    array_push($device_name_bt, $row->device_name);

                    $first_row_2 = 1;
                    //  echo 'IN at first_row_2 = 1  <br>';
                    continue;
                }
                if ($k > 0) {
                    $j = $k;
                    $j--;

                    $datetime1 = new DateTime($at[$j]['todate']);
                    $datetime2 = new DateTime($row->datetime);
                    $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                    $pieces = explode(":", $subtotal);
                    if ($pieces[0] > 16) {
                        if (isset($bt[$a]['fromdate'])) {
                            array_push($bt_id, $row->primary_id);
                            array_push($in_out_time_bt, $row->type);
                            array_push($device_name_bt, $row->device_name);
                            //  echo 'IN bt first_row_2 = 1 - > 9 <br>';

                            continue;
                        }
                        $bt[$a]['fromdate'] = $row->datetime;
                        $bt[$a]['statusin'] = $row->type;
                        array_push($bt_id, $row->primary_id);
                        array_push($in_out_time_bt, $row->type);
                        array_push($device_name_bt, $row->device_name);
                        //  echo 'IN bt first_row_2 = 1 - < 9 <br>';

                        $first_row_2 = 1;
                        continue;
                    }
                }
                array_push($at_id, $row->primary_id);
                array_push($in_out_time_at, $row->type);
                array_push($device_name_at, $row->device_name);

                $at[$k]['fromdate'] = $row->datetime;
                $at[$k]['statusin'] = $row->type;
                $first_row = 1;
                continue;
            }

            if ($row->type == "OUT") {
                if ($first_row_2 == 0) {
                    if (isset($at[$k]['fromdate']) && $at[$k]['fromdate'] != "") {
                        $datetime1 = new DateTime($at[$k]['fromdate']);
                        $datetime2 = new DateTime($row->datetime);
                        $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                        $pieces = explode(":", $subtotal);
                        if ($pieces[0] > 16) {
                            array_push($at_id, $row->primary_id);
                            array_push($in_out_time_at, $row->type);
                            array_push($device_name_at, $row->device_name);
                            // echo 'OUT at first_row_2 = 0  <br>';

                            continue;
                        }
                        $at[$k]['statusout'] = $row->type;
                        $at[$k]['todate'] = $row->datetime;
                        $at[$k]['subtotalhours'] = $subtotal;
                        array_push($at_id, $row->primary_id);
                        array_push($in_out_time_at, $row->type);
                        array_push($device_name_at, $row->device_name);
                        //  echo 'OUT at first_row_2 = 0  <br>';

                        $k++;
                        continue;
                    } elseif (!isset($at[$k]['todate'])) {
                        if (isset($at[$j]['fromdate'])) {
                            $j = $k;
                            $j--;
                            $datetime1 = new DateTime($at[$j]['fromdate']);
                            $datetime2 = new DateTime($row->datetime);
                            $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                            $at[$j]['todate'] = $row->datetime;
                            $at[$j]['statusout'] = $row->type;
                            $at[$j]['subtotalhours'] = $subtotal;
                            array_push($at_id, $row->primary_id);
                            array_push($in_out_time_at, $row->type);
                            array_push($device_name_at, $row->device_name);
                            //  echo 'OUT at first_row_2 != 0 <br>';

                            continue;
                        }
                    }
                } else {
                    if (isset($bt[$a]['fromdate']) && $bt[$a]['fromdate'] != "") {
                        $bt[$a]['statusout'] = $row->type;
                        $bt[$a]['todate'] = $row->datetime;
                        $datetime1 = new DateTime($bt[$a]['fromdate']);
                        $datetime2 = new DateTime($row->datetime);
                        $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                        $bt[$a]['subtotalhours'] = $subtotal;
                        array_push($bt_id, $row->primary_id);
                        array_push($in_out_time_bt, $row->type);
                        array_push($device_name_bt, $row->device_name);
                        //  echo 'isset bt fromdate  <br>';

                        $a++;
                        continue;
                    } elseif (!isset($bt[$a]['todate'])) {
                        $j = $a;
                        $j--;
                        if (isset($bt[$j]['fromdate'])) {
                            $datetime1 = new DateTime($bt[$j]['fromdate']);
                            $datetime2 = new DateTime($row->datetime);
                            $subtotal = $this->calculate_hours_mins($datetime1, $datetime2);
                            $bt[$j]['todate'] = $row->datetime;
                            $bt[$j]['statusout'] = $row->type;
                            $bt[$j]['subtotalhours'] = $subtotal;
                            array_push($bt_id, $row->primary_id);
                            array_push($in_out_time_bt, $row->type);
                            array_push($device_name_bt, $row->device_name);
                            //  echo 'isset bt fromdate !todate  <br>';

                            continue;
                        }
                    }
                }
            }
        }

        if (count($at) > 0) {
            if (!isset($at[count($at) - 1]['todate'])) {
                unset($at[count($at) - 1]);
            }
        }

        if (count($bt) > 0) {
            if (!isset($bt[count($bt) - 1]['todate'])) {
                unset($bt[count($bt) - 1]);
            }
        }

        for ($i = 0; $i < count($at); $i++) {
            $at[$i]['fromdate'] . "  -  " . $at[$i]['todate'] . "  ---  " . $at[$i]['subtotalhours'];
            "<br>";
        }
        $work1 = $this->calculate_total_working_hours($at);

        "<br>-------------------------<br>";
        for ($i = 0; $i < count($bt); $i++) {
            $bt[$i]['fromdate'] . "  -  " . $bt[$i]['todate'] . "  ---  " . $bt[$i]['subtotalhours'];
            "<br>";
        }
        $work2 = $this->calculate_total_working_hours($bt);
        $work2;

        if (count($bt) > 0) {
            $work1_hours = explode(":", $work1);
            $work2_hours = explode(":", $work2);
            if ($work2_hours > $work1_hours) {
                $at = $bt;
                $at_id = $bt_id;
                $in_out_time_at = $in_out_time_bt;
                $device_name_at = $device_name_bt;
            }
        }

        for ($i = 0; $i <= count($at_id) - 1; $i++) {
            $sql = "update ms_sql3 set status=1 where primary_id=" . $at_id[$i];
            //echo "<br>".$sql."</br>";
            //$mysqli->query($sql);
        }
        // echo "<br><pre>";
        // print_r($at_id);
        // print_r($in_out_time_at);
        // echo "</br>";

        if (isset($at[0]['fromdate'])) {
            $currnet_date = Carbon::createFromFormat('Y-m-d H:i:s', $at[0]['fromdate'])->format('Y-m-d');
            $from_date = Carbon::createFromFormat('Y-m-d H:i:s', $date_from)->format('Y-m-d');
            if ($currnet_date != $from_date) {
                $k = 0;
                $a = 0;
                $first_row = 0;
                $at = [];
                $bt = [];
                $first_row_2 = 0;
                $at_id = [];
                $bt_id = [];
                $in_out_time_at = [];
                $in_out_time_bt = [];
                $device_name_at = [];
                $device_name_bt = [];
                $attendance_data = [];
            }
        }

        $update_status = true;

        if (count($at) > 0) {
            foreach ($at_id as $primary_id) {

                // echo 'Primary ID ' . $primary_id;
                $upd_to_date = $at[count($at) - 1]['todate'];
                $check_by_primary = MsSql::where('primary_id', $primary_id)->first();

                if ($update_status == true) {

                    $update = DB::table('ms_sql')->where('primary_id', $primary_id)->update(['status' => 1]);
                    // echo "<br>";
                    // echo "Update status = " . $update_status;
                    // echo "<br>";
                }
                if ($upd_to_date == $check_by_primary->datetime) {
                    $update_status = false;
                }
            }
        }

        // Attendance data set return values...................................
        for ($i = 0; $i < count($at); $i++) {

            if ($i == 0) {
                $attendance_data['date'] = date('Y-m-d', strtotime($at[$i]['fromdate']));
                $attendance_data['in_time'] = $at[$i]['fromdate'];
                $attendance_data['finger_print_id'] = $finger_id->finger_id;
                // $attendance_data['finger_print_id'] = $finger_id['ID'];
            }
            $attendance_data['out_time'] = $at[count($at) - 1]['todate'];
            // $attendance_data['working_time'] = $this->calculate_total_working_hours($at);
            $attendance_data['working_time'] = $this->workingtime($at[0]['fromdate'], $at[count($at) - 1]['todate']);
            $attendance_data['working_hour'] = $this->calculate_total_working_hours($at);
            // $attendance_data['working_hour'] = $this->workingtime($at[0]['fromdate'], $at[count($at) - 1]['todate']);
            $attendance_data['created_at'] = date('Y-m-d H:i:s');
            $attendance_data['updated_at'] = date('Y-m-d H:i:s');
            $attendance_data['created_by'] = auth()->user() ? auth()->user()->user_id : null;
            $attendance_data['updated_by'] = auth()->user() ? auth()->user()->user_id : null;
        }

        if ($attendance_data != []) {
            if (count($at) > 0 && count($at_id) > 0) {

                $attendance_data['in_out_time'] = $this->in_out_time($at_id, $in_out_time_at, $device_name_at);
            }
        }

        // echo "<pre>";
        // print_r($at);
        // print_r($attendance_data);
        // echo "</pre>";

        return $attendance_data;
    }

    public function in_out_time($at_id, $in_out_time_at, $device_name_at)
    {
        $result = [];
        $array_values = array_values($at_id);
        $array_values = json_encode($at_id);

        foreach ($at_id as $key => $primary_id) {
            $in_out_time = DB::table('ms_sql')->where('primary_id', $primary_id)->select('datetime')->first();
            $result[] = date('H:i', strtotime($in_out_time->datetime)) . ':' . $in_out_time_at[$key] . ' ' . '(' . $device_name_at[$key] . ')';
        }
        // dd($result);
        $str = json_encode($result);
        $str = str_replace('[', ' ', $str);
        $str = str_replace(']', ' ', $str);
        $str = str_replace('"', ' ', $str);
        // dd($str);
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

    public function find_work_shift()
    {
        // $actual_datetime, $shift_datetime

        $shift_list = WorkShift::all();

        $day = 5;
        $finger_id['ID'] = 'P001';
        // dd($finger_id);

        $start = sprintf("%02d", $day);
        $date = '2022-07' . '-' . $start . '';
        // dd($date);

        $start_date = DATE('Y-m-d', strtotime($date)) . " 05:00:00";
        $end_date = DATE('Y-m-d', strtotime($date . " +1 day")) . " 08:00:00";

        $data_format = $this->calculate_attendance($start_date, $end_date, $finger_id, false);
        dump($data_format);

        //     if (isset($data_format)) {
        //         foreach ($shift_list as $key => $value) {
        //             // dd();
        //             $datetime1 = new DateTime($data_format['in_time']);
        //             $datetime2 = new DateTime($value['start_time']);
        //             if ($datetime1 >=  $datetime2) {
        //                 $interval = $datetime1->diff($datetime2);
        //                 echo $interval->format('%h') . " Hours " . $interval->format('%i') . " Minutes";
        //             } else {
        //                 $interval = $datetime2->diff($datetime1);
        //                 echo $interval->format('%h') . " Hours " . $interval->format('%i') . " Minutes";
        //             }
        //         }
        //     }
    }

    public function find_closest_time($dates, $first_in)
    {

        function closest($dates, $findate)
        {
            $newDates = array();

            foreach ($dates as $date) {
                $newDates[] = strtotime($date);
            }

            echo "<pre>";
            print_r($newDates);
            echo "</pre>";

            sort($newDates);
            foreach ($newDates as $a) {
                if ($a >= strtotime($findate)) {
                    return $a;
                }
            }
            return end($newDates);
        }

        $values = closest($dates, date('Y-m-d H:i:s', \strtotime($first_in)));
        echo date('Y-m-d H:i:s', $values);
    }

    public function shift_timing_array($in_time, $start_shift, $end_shift)
    {
        $shift_status = $in_time <= $end_shift && $in_time >= $start_shift;
        return $shift_status;
    }

    public function find_device_name($mystring)
    {
        // $mystring = "Main Door Exit";
        $devices_name = '';
        $devices = ['Service Door', 'Main Door'];
        // $devices = ['Service Door Exit', 'service door entry', 'Main Door entry', 'Main door exit'];

        // Test if string contains the word
        if (strpos($mystring, $devices[0]) !== false) {
            $devices_name = 'SD';
        } elseif (strpos($mystring, $devices[1]) !== false) {
            $devices_name = 'MD';
        }

        echo '<br>';
        echo $devices_name;
        return $devices_name;
    }

    public function workingtime($from, $to)
    {
        $date1 = new DateTime($to);
        $date2 = $date1->diff(new DateTime($from));
        $hours = ($date2->days * 24);
        $hours = $hours + $date2->h;

        return $hours . ":" . $date2->i . ":" . $date2->s;
    }

    public function in_out_time_list($in_out_time_list)
    {
        $result = [];

        foreach ($in_out_time_list as $key => $in_out_time) {
            $result[] = $in_out_time;
        }

        $str = json_encode($result);
        $str = str_replace('[', ' ', $str);
        $str = str_replace(']', ' ', $str);
        $str = str_replace('"', ' ', $str);
        return $str;
    }

    public function dd()
    {
        $data = [
            'BRM9193360148',
            'BRM9193360137',
            'BRM9193360058',
            'BRM9193360034',
            'BRM9193360025',
            'BRM9192960031',
            'BRM9193360059',
            'BRM9191060473',
            'BRM9193360057',
            'BRM9193360055',
        ];
    }
}
