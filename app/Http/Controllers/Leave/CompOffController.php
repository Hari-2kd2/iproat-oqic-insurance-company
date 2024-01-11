<?php

namespace App\Http\Controllers\Leave;

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

    public function index()
    {
        $results = CompOff::with('employee')->orderBy('comp_off_details_id', 'desc')->get();
        return view('admin.leave.compOff.index', ['results' => $results]);
    }

    public function create()
    {
        $employeeList = $this->commonRepository->compOffEligibleEmployeeFingerList();
        $offTimingList = $this->commonRepository->leaveTimingList();
        return view('admin.leave.compOff.form', ['employeeList' => $employeeList, 'offTimingList' => $offTimingList]);
    }

    public function store(CompOffRequest $request)
    {
        $input = $request->all(); 
        $employee = Employee::where('finger_id', $request->finger_print_id)->where('salary_limit', 1)->first();

        if (!$employee) {
            return  redirect('compOff')->with('error', 'Not eligible for Comp Off');
        }

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
                $compOffDetails = CompOff::create($input);
                return redirect('compOff')->with('success', 'Comp off successfully saved.');
            } else {
                return redirect('compOff')->with('error', 'Insuffient working hours ' . $off_timing);
            }
            
        } catch (\Exception $e) {
            $bug = 1;
            // info($e->getMessage());
            return redirect('compOff')->with('error', 'Something Went Wrong');
        }
    }

    public function differenceInHours($startdate, $enddate)
    {
        $starttimestamp = strtotime($startdate);
        $endtimestamp = strtotime($enddate);
        $difference = abs($starttimestamp - $endtimestamp) / 3600;
        return $difference;
    }

    public function edit($id)
    {
        $employeeList = $this->commonRepository->employeeFingerList();
        $offTimingList = $this->commonRepository->leaveTimingList();
        $editModeData = CompOff::findOrFail($id);
        return view('admin.leave.compOff.form', ['editModeData' => $editModeData, 'offTimingList' => $offTimingList, 'employeeList' => $employeeList]);
    }

    public function update(CompOffRequest $request, $id)
    {
        $compOffDetails = CompOff::findOrFail($id);
        $input = $request->all();
        $input['off_date'] = dateConvertFormtoDB($input['off_date']);
        $input['working_date'] = dateConvertFormtoDB($input['working_date']);
        try {
            $compOffDetails->update($input);
            $employeeInOutData = EmployeeInOutData::where('finger_print_id', $compOffDetails->finger_print_id)->where('date', $compOffDetails->off_date)->update(['comp_off_details_id' => $compOffDetails->comp_off_details_id]);

            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Comp off successfully updated. ');
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
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
        $compOff = CompOff::where('finger_print_id', $request->finger_print_id)->where('off_date', dateConvertFormtoDB($request->off_date))->get();
        $off_date = dateConvertFormtoDB($request->off_date);
        $start_date = date('Y-m-d', strtotime('-45 days', strtotime($off_date)));
        $end_date = date('Y-m-d', strtotime($off_date));
        $employee = Employee::where('finger_id', $request->finger_print_id)->first();

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
            return 'Exists';
        }

        $weekDaysResults = EmployeeInOutData::whereIn('date', array_values($dateArr['week_days']))->where('finger_print_id', $employee->finger_id)->where('working_time', '<=', '15:59:59')->where('working_time', '>=', '12:00:00');
        $fullWeekDaysResults = EmployeeInOutData::whereIn('date', array_values($dateArr['week_days']))->where('finger_print_id', $employee->finger_id)->where('working_time', '>=', '16:00:00');
        $holidaysResults = EmployeeInOutData::whereIn('date', array_values($dateArr['holidays']))->where('finger_print_id', $employee->finger_id)->where('working_time', '<=', '07:59:59')->where('working_time', '>=', '04:00:00');
        $fullHolidaysResults = EmployeeInOutData::whereIn('date', array_values($dateArr['holidays']))->where('finger_print_id', $employee->finger_id)->where('working_time', '>=', '08:00:00');
        $compOffMonth = CompOff::where('finger_print_id', $request->finger_print_id)->get();

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

        $options = [];

        foreach ($weekDaysResults as $key => $value) {
            $options[$value->employee_attendance_id] = $value->working_time != null ? dateConvertDBtoForm($value->date) . ' ' . date('H:i:s', strtotime($value->working_time)) . ' (Week Day)' : '00:00' . ' working time on date ' . dateConvertDBtoForm($value->date);
        }

        foreach ($holidaysResults as $key => $value) {
            $options[$value->employee_attendance_id] = $value->working_time != null ? dateConvertDBtoForm($value->date) . ' ' . date('H:i:s', strtotime($value->working_time)) . ' (Holiday / Week Off)'  : '00:00' . ' working time on date ' . dateConvertDBtoForm($value->date);
        }
        foreach ($fullWeekDaysResults as $key => $value) {
            $options[$value->employee_attendance_id] = $value->working_time != null ? dateConvertDBtoForm($value->date) . ' ' . date('H:i:s', strtotime($value->working_time)) . ' (Week Day)'  : '00:00' . ' working time on date ' . dateConvertDBtoForm($value->date);
        }
        foreach ($fullHolidaysResults as $key => $value) {
            $options[$value->employee_attendance_id] = $value->working_time != null ? dateConvertDBtoForm($value->date) . ' ' . date('H:i:s', strtotime($value->working_time)) . ' (Holiday / Week Off)'  : '00:00' . ' working time on date ' . dateConvertDBtoForm($value->date);
        }


        return count($options) > 0 ? $options : 'notFound';
    }

    public function compOffTemplate(Request $request)
    {
        $date = dateConvertFormtoDB($request->date);
        $inc = 1;
        $dataSet = [];
        $Data = EmployeeInOutData::where('date', $date)->where('working_time', '>=', AppConstant::$HALF_DAY_HOUR)->orderBy('finger_print_id', 'ASC')->get();

        foreach ($Data as $key => $data) {

            $dataSet[] = [
                $inc,
                $data->finger_print_id,
                $data->date,
                $data->working_time,
                $data->working_time,
                'Simple Approval',
            ];

            $inc++;
        }

        $primaryHead = ['SL.NO', 'EMPLOYEE ID', 'DATE', 'ACTUAL Wrk.Hr', 'APPROVED Wrk.Hr', 'REMARK'];
        $heading = [$primaryHead];

        $extraData['heading'] = $heading;
        $filename = 'Employee Overtime Information-' . DATE('d-m-Y His') . '.xlsx';

        return Excel::download(new ApproveOvertimeReport($dataSet, $extraData), $filename);
    }

    public function import(Request $request)
    {
        try {

            $date = dateConvertFormtoDB($request->date);
            $file = $request->file('select_file');
            Excel::import(new ApprovedOvertimeImport($request->all()), $file);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $import = new ApprovedOvertimeImport();
            $import->import($file);

            foreach ($import->failures() as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }
        return back()->with('success', 'Approve Overtime information imported successfully.');
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
