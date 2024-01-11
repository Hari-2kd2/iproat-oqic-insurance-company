<?php

namespace App\Http\Controllers\Api;

use App\Model\CompOff;
use App\Model\Employee;
use App\Components\Common;
use App\Model\WeeklyHoliday;
use Illuminate\Http\Request;
use App\Model\EmployeeInOutData;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\CompOffRequest;
use App\Lib\Enumerations\AppConstant;
use App\Exports\ApproveOvertimeReport;
use App\Repositories\CommonRepository;
use App\Imports\ApprovedOvertimeImport;
use App\Lib\Enumerations\AttendanceStatus;

class CompOffController extends Controller
{

    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }

    public function index(Request $request)
    {
        // $results = [];
        $employee = Employee::where('employee_id', $request->employee_id)->first();
        if (isset($employee->salary_limit) && $employee->salary_limit == 1) {
            $results = CompOff::with('employee')->where('employee_id', $request->employee_id)->orderBy('comp_off_details_id', 'desc')->get();

            if (count($results) > 0) {
                return response()->json([
                    'status'       => true,
                    'data'         => $results,
                    'message'      => 'Comp off Details Successfully Received',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No Data Found',
                ], 200);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Not eligible for comp off',
            ], 200);
        }
    }


    public function create()
    {
        $employeeList = $this->commonRepository->compOffEligibleEmployeeFingerList();
        $offTimingList = $this->commonRepository->leaveTimingList();
        return view('admin.leave.compOff.form', ['employeeList' => $employeeList, 'offTimingList' => $offTimingList]);
    }

    public function store(CompOffRequest $request)
    {

        if ($request->employee_id == '') {
            return response()->json([
                'message' => 'Employee Data not found',
                'status' => false,
            ], 200);
        } elseif (($request->off_date == '' || $request->off_date == '0000-00-00')) {
            return response()->json([
                'message' => 'Off Date Not Given',
                'status' => false,
            ], 200);
        } elseif (($request->working_date == '')) {
            return response()->json([
                'message' => 'Working Date Not Given',
                'status' => false,
            ], 200);
        } elseif ($request->off_timing == '') {
            return response()->json([
                'message' => 'Off Timing Not Given',
                'status' => false,
            ], 200);
        } else {

            // $input = $request->all();
            $employee = Employee::where('employee_id', $request->employee_id)->first();

            // if (!$employee) {
            //     return response()->json([
            //         'message' => 'Not eligible for Comp Off',
            //         'status' => false,
            //     ], 200);
            // }

            $input = $request->all();
            $employeeInOutData = EmployeeInOutData::where('employee_attendance_id', $request->working_date)->first();

            $input['off_date'] = dateConvertFormtoDB($input['off_date']);
            $input['working_date'] = $employeeInOutData->date;
            $input['employee_id']  = $employee->employee_id;
            $input['branch_id']  = $employee->branch_id;
            $off_date = dateConvertFormtoDB($request->off_date);
            $compensate_eligible = 0;


            $check_date = dateConvertFormtoDB($employeeInOutData->date);
            $start_date = date('Y-m-d', strtotime('-45 days', strtotime($input['off_date'])));
            $end_date = date('Y-m-d', strtotime('+45 days', strtotime($input['off_date'])));


            $govtHolidays = DB::select(DB::raw('call SP_getHoliday("' . $start_date . '","' . $end_date . '")'));
            $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday("' . $employee->employee_id . '","' . date('Y-m',  strtotime($start_date)) . '")'));
            $weeklyHolidaysDates = WeeklyHoliday::where('employee_id', $employee->employee_id)->where('month', date('Y-m', strtotime($start_date)))->first();

            $day_status = 'Weekday';

            $ifHoliday = $this->ifHoliday($govtHolidays, $employeeInOutData->date, $employee->employee_id, $weeklyHolidays, $weeklyHolidaysDates);

            if ($ifHoliday) {
                $day_status = 'Holiday';
            }


            try {
                $balance = "00.00";
                if ($day_status == 'Weekday') {
                    if ($employeeInOutData->working_time >= '12:00:00' && $request->off_timing == 0) {
                        $compensate_eligible = 1;
                        $balance = $this->differenceInHours($employeeInOutData->working_time, "12:00:00");
                    } elseif ($employeeInOutData->working_time >= '16:00:00' && $request->off_timing == 1) {
                        $compensate_eligible = 1;
                        $balance = $this->differenceInHours($employeeInOutData->working_time, "16:00:00");
                    }
                } elseif ($day_status == 'Holiday') {
                    if ($employeeInOutData->working_time >= '04:00:00' && $request->off_timing == 0) {
                        $compensate_eligible = 1;
                        $balance = $this->differenceInHours($employeeInOutData->working_time, "04:00:00");
                    } elseif ($employeeInOutData->working_time >= '08:00:00' && $request->off_timing == 1) {
                        $compensate_eligible = 1;
                        $balance = $this->differenceInHours($employeeInOutData->working_time, "08:00:00");
                    }
                } else {
                    $compensate_eligible = 0;
                    $balance = "00.00";
                }


                $empovertime = 0;

                $ot_hours = explode(".", $balance);
                $hours = $ot_hours[0];
                //  dd($ot_hours); 
                $balance_time = $hours . ':00:00';
                // $balance_time = date('H:i:s', strtotime($hours));   

                if ($request->off_timing == 0) {
                    $off_timing = 'for half day';
                } elseif ($request->off_timing == 1) {
                    $off_timing = 'for full day';
                } else {
                    $off_timing = '';
                }


                // $employeeInOutData = EmployeeInOutData::where('finger_print_id', $input['finger_print_id'])->where('date', $input['off_date'])->first();
                if ($compensate_eligible == 1) {
                    $input['employee_attendance_id'] = $employeeInOutData->employee_attendance_id;
                    $input['off_timing'] = $request->off_timing;
                    $input['balance_hour'] = $balance_time;
                    $input['finger_print_id'] = $employeeInOutData->finger_print_id;

                    $compOffDetails = CompOff::create($input);

                    return response()->json([
                        'message' => "Comp off successfully saved !!!",
                        'status' => true,
                    ], 200);
                } else {

                    return response()->json([
                        'message' => 'Insuffient working hours ' . $off_timing,
                        'status' => false,
                    ], 200);
                }
            } catch (\Exception $e) {

                // info($e->getMessage()); 
                return response()->json([
                    'message' => "Something Went Wrong",
                    'status' => false,
                ], 200);
            }
        }
    }

    public function differenceInHours($startdate, $enddate)
    {
        $starttimestamp = strtotime($startdate);
        $endtimestamp = strtotime($enddate);
        $difference = abs($starttimestamp - $endtimestamp) / 3600;
        return $difference;
    }


    public function destroy($id)
    {
        try {
            $compOffDetails = CompOff::findOrFail($id);
            $compOffDetails->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            echo "success";
        }
        //  elseif ($bug == 1451) {
        //     echo 'hasForeignKey';
        // }
        else {
            echo 'error';
        }
    }

    public function getWorkingtime(Request $request)
    {
        $compOff = CompOff::where('employee_id', $request->employee_id)->where('off_date', dateConvertFormtoDB($request->off_date))->get();
        $off_date = dateConvertFormtoDB($request->off_date);
        $start_date = date('Y-m-d', strtotime('-45 days', strtotime($off_date)));
        $end_date = date('Y-m-d', strtotime($off_date));
        $employee = Employee::where('employee_id', $request->employee_id)->first();

        $govtHolidays = DB::select(DB::raw('call SP_getHoliday("' . $start_date . '","' . $end_date . '")'));
        $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday("' . $employee->employee_id . '","' . date('Y-m',  strtotime($start_date)) . '")'));
        $weeklyHolidaysDates = WeeklyHoliday::where('employee_id', $employee->employee_id)->where('month', date('Y-m', strtotime($start_date)))->first();
        $data = findFromDateToDateToAllDate($start_date, $end_date);
        $dateArr['holidays'] = $dateArr['week_days']  = [];

        foreach ($data as $key => $value) {
            if ($value['date'] != $off_date) {
                $ifHoliday = $this->ifHoliday($govtHolidays, $value['date'], $employee->employee_id, $weeklyHolidays, $weeklyHolidaysDates);

                if ($ifHoliday) {
                    $dateArr['holidays'][] = $value['date'];
                }

                $dateArr['week_days'][] = $value['date'];
            }
        }

        if (count($compOff) > 2) {
            return response()->json([
                'status' => false,
                'message' => 'Exists',
            ], 200);
        }

        $weekDaysResults = EmployeeInOutData::whereIn('date', array_values($dateArr['week_days']))->where('finger_print_id', $employee->finger_id)->where('working_time', '<=', '15:59:59')->where('working_time', '>=', '12:00:00');
        $fullWeekDaysResults = EmployeeInOutData::whereIn('date', array_values($dateArr['week_days']))->where('finger_print_id', $employee->finger_id)->where('working_time', '>=', '16:00:00');
        $holidaysResults = EmployeeInOutData::whereIn('date', array_values($dateArr['holidays']))->where('finger_print_id', $employee->finger_id)->where('working_time', '<=', '07:59:59')->where('working_time', '>=', '04:00:00');
        $fullHolidaysResults = EmployeeInOutData::whereIn('date', array_values($dateArr['holidays']))->where('finger_print_id', $employee->finger_id)->where('working_time', '>=', '08:00:00');

        $compOffMonth = CompOff::where('finger_print_id', $employee->finger_id)->get();

        if (count($compOffMonth) > 0) {

            $existsDateArr = [];
            foreach ($compOffMonth as $key => $value) {
                $existsDateArr[] = $value->working_date;
            }

            $weekDaysResults = $weekDaysResults->whereNotIn('date', $existsDateArr);
            $holidaysResults = $holidaysResults->whereNotIn('date', $existsDateArr);
            $fullWeekDaysResults = $fullWeekDaysResults->whereNotIn('date', $existsDateArr);
            $fullHolidaysResults = $fullHolidaysResults->whereNotIn('date', $existsDateArr);
        }

        $weekDaysResults = $weekDaysResults->get();
        $holidaysResults = $holidaysResults->get();
        $fullWeekDaysResults = $fullWeekDaysResults->get();
        $fullHolidaysResults = $fullHolidaysResults->get();

        $options = array();

        foreach ($weekDaysResults as $key => $value) {

            $options[] = array(
                'employee_attendance_id' => $value->employee_attendance_id,
                'attendance_option' =>  $value->working_time != null ? dateConvertDBtoForm($value->date) . ' ' . date('H:i:s', strtotime($value->working_time)) . ' (Week Day)' : '00:00' . ' working time on date ' . dateConvertDBtoForm($value->date),
            );
        }

        foreach ($holidaysResults as $key => $value) {
            $options[] = array(
                "employee_attendance_id" => $value->employee_attendance_id,
                "attendance_option" => $value->working_time != null ? dateConvertDBtoForm($value->date) . ' ' . date('H:i:s', strtotime($value->working_time)) . ' (Holiday / Week Off)'  : '00:00' . ' working time on date ' . dateConvertDBtoForm($value->date),
            );
        }
        foreach ($fullWeekDaysResults as $key => $value) {
            $options[] = array(
                "employee_attendance_id" => $value->employee_attendance_id,
                "attendance_option" =>  $value->working_time != null ? dateConvertDBtoForm($value->date) . ' ' . date('H:i:s', strtotime($value->working_time)) . ' (Week Day)'  : '00:00' . ' working time on date ' . dateConvertDBtoForm($value->date),
            );
        }
        foreach ($fullHolidaysResults as $key => $value) {
            $options[] = array(
                "employee_attendance_id" => $value->employee_attendance_id,
                "attendance_option" =>  $value->working_time != null ? dateConvertDBtoForm($value->date) . ' ' . date('H:i:s', strtotime($value->working_time)) . ' (Holiday / Week Off)'  : '00:00' . ' working time on date ' . dateConvertDBtoForm($value->date),
            );
        }


        if (count($options) > 0) {

            return response()->json([
                'message' => "Data Found !!!",
                'status' => true,
                'data' => $options,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'notFound',
            ], 200);
        }
    }


    public function ifHoliday($govtHolidays, $date, $employee_id, $weeklyHolidays, $weeklyHolidaysDates)
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

        $timestamp = strtotime($date);
        $dayName = date("l", $timestamp);
        foreach ($weeklyHolidays as $v) {
            if ($v->day_name == $dayName && $v->employee_id == $employee_id && isset($weeklyHolidaysDates) && $dayName == $weeklyHolidaysDates['day_name']) {
                return true;
            }
        }

        return false;
    }
}
