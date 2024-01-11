<?php

namespace App\Http\Controllers\Leave;

use App\User;
use Carbon\Carbon;
use App\Model\Employee;
use App\Model\LeaveType;
use App\Components\Common;
use Illuminate\Http\Request;
use App\Model\LeaveApplication;
use App\Mail\LeaveApplicationMail;
use Illuminate\Support\Facades\DB;
use App\Model\PaidLeaveApplication;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Repositories\LeaveRepository;
use App\Repositories\CommonRepository;
use App\Notifications\LeaveNotification;
use App\Http\Requests\ApplyForLeaveRequest;
use Illuminate\Support\Facades\Notification;

class ApplyForLeaveController extends Controller
{

    protected $commonRepository;
    protected $leaveRepository;

    public function __construct(CommonRepository $commonRepository, LeaveRepository $leaveRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->leaveRepository = $leaveRepository;
    }

    public function index()
    {
        $results = LeaveApplication::with(['employee', 'leaveType', 'approveBy', 'rejectBy'])
            ->where('employee_id', (session('logged_session_data.employee_id')))
            ->orderBy('leave_application_id', 'desc')
            ->paginate(10);

        return view('admin.leave.applyForLeave.index', ['results' => $results]);
    }

    public function create()
    {
        $leaveTypeList = $this->commonRepository->leaveTypeList();
        $getEmployeeInfo = $this->commonRepository->getEmployeeInfo(Auth::user()->user_id);

        $leaveType = LeaveType::sum('num_of_day');
        $totalPaidLeaveTaken = PaidLeaveApplication::where('employee_id', Auth::user()->user_id)->where('status', 2)->where('created_at', Carbon::now()->year)->pluck('number_of_day');
        $totalLeaveTaken = LeaveApplication::where('employee_id', Auth::user()->user_id)->where('status', 2)->whereYear('created_at', Carbon::now()->year)->pluck('number_of_day');
        $sumOfLeaveTaken = (int) $totalLeaveTaken->sum();
        $permissableLeave = $leaveType;
        $checkLeaveEligibility = $sumOfLeaveTaken <= $permissableLeave;
        $leaveBalance = $leaveType - $sumOfLeaveTaken;

        $data = [
            'checkLeaveEligibility' => $checkLeaveEligibility == true ? 'Eligibile' : 'Not Eligibile',
            'leaveType' => $leaveType,
            'sumOfLeaveTaken' => $sumOfLeaveTaken,
            'leaveBalance' => $leaveBalance,
            'leaveTypeList' => $leaveTypeList,
            'permissableLeave' => $permissableLeave,
            'totalLeaveTaken' => $totalLeaveTaken,
            'totalPaidLeaveTaken' => $totalPaidLeaveTaken,
        ];

        return view('admin.leave.applyForLeave.leave_application_form', ['leaveTypeList' => $leaveTypeList, 'data' => $data, 'getEmployeeInfo' => $getEmployeeInfo]);
    }

    public function getEmployeeLeaveBalance(Request $request)
    {
        $leave_type_id = $request->leave_type_id;
        $employee_id = $request->employee_id;
        if ($leave_type_id != '' && $employee_id != '') {
            return $this->leaveRepository->calculateEmployeeLeaveBalance($leave_type_id, $employee_id);
        }
    }

    public function applyForTotalNumberOfDays(Request $request)
    {
        $application_from_date = dateConvertFormtoDB($request->application_from_date);
        $application_to_date = dateConvertFormtoDB($request->application_to_date);
        $array = $this->leaveRepository->calculateTotalNumberOfLeaveDays($application_from_date, $application_to_date, $request->employee_id);

        return $array['countDay'];
    }


    public function applyForTotalNumberOfDays1(Request $request)
    {
        $application_from_date = dateConvertFormtoDB($request->application_from_date);
        $application_to_date = dateConvertFormtoDB($request->application_to_date);
        $array = $this->leaveRepository->calculateTotalNumberOfLeaveDays($application_from_date, $application_to_date, $request->employee_id);

        $count = 0;
        if ($array['countDay'] > 0) {
            $pHoliday = $array['public_holidays'];
            $wHoliday = $array['weekly_holidays'];
            $holidayArray = array_merge($pHoliday, $wHoliday);
            $dateRange = $array['date_range'];
        }
        $holidayDate = [];

        foreach ($dateRange as $key => $value) {
            if (in_array($value, $holidayArray)) {
                $holidayDate[] = $value;
            }
        }

        $lastLoop = '';
        $skipHoliday = 0;

        foreach ($holidayDate as $key => $value) {

            if (date('Y-m-d', strtotime('+1 days', strtotime($lastLoop))) != $value) {
                $count = 0;
            }

            $lastLoop = $value;

            if (in_array(date('Y-m-d', strtotime('+1 days', strtotime($value))), $dateRange) && in_array(date('Y-m-d', strtotime('-1 days', strtotime($value))), $dateRange)) {
                $count++;
                $skipHoliday += $count;
            }
        }

        if ($skipHoliday > 0) {
            return $array['countDay'] + $skipHoliday;
        } else {
            return   $array['countDay'];
        }
    }

    public function store(ApplyForLeaveRequest $request)
    {
        $input = $request->all();
        $input['application_from_date'] = dateConvertFormtoDB($request->application_from_date);
        $input['application_to_date'] = dateConvertFormtoDB($request->application_to_date);
        $input['application_date'] = date('Y-m-d');
        $input['branch_id'] = auth()->user()->branch_id;

        $employee = Employee::where('employee_id', session('logged_session_data.employee_id'))->first();
        $hod = Employee::where('employee_id', $employee->supervisor_id)->first();

        try {
            DB::beginTransaction();

            $checkLeave = LeaveApplication::where('application_from_date', $input['application_from_date'])->where('application_to_date', $input['application_to_date'])
                ->where('employee_id', $input['employee_id'])->where('status', '!=', 3)->first();

            if (!$checkLeave) {
                $data = LeaveApplication::create($input);
                $leaveType = LeaveType::where('leave_type_id', $input['leave_type_id'])->first();

                if ($hod->email) {
                    $maildata = Common::mail('emails/mail', $hod->email, 'Leave Request Notification', ['head_name' => $hod->first_name . ' ' . $hod->last_name, 'request_info' => $employee->first_name . ' ' . $employee->last_name . '. have requested for leave (Purpose: ' . $request->purpose . ') from ' . ' ' . dateConvertFormtoDB($request->application_from_date) . ' to ' . dateConvertFormtoDB($request->application_to_date), 'status_info' => '']);
                }

                $bug = 0;
            } else {
                $bug = 3;
            }


            DB::commit();
        } catch (\Exception $e) {
            $bug = 1;
            DB::rollback();
        }

        if ($bug == 0) {
            return redirect('applyForLeave')->with('success', 'Leave application successfully send.');
        } elseif ($bug == 3) {
            return redirect('applyForLeave')->with('error', 'Leave application already exists.');
        } else {
            return redirect('applyForLeave')->with('error', 'Something error found !, Please try again.');
        }
    }
}
