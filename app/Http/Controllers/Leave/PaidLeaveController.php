<?php

namespace App\Http\Controllers\Leave;

use Carbon\Carbon;
use App\Model\LeaveType;
use Illuminate\Http\Request;
use App\Model\LeaveApplication;
use App\Model\PaidLeaveApplication;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\LeaveRepository;
use App\Repositories\CommonRepository;
use App\Http\Requests\ApplyForPaidLeaveRequest;

class PaidLeaveController extends Controller
{

    protected $commonRepository;
    protected $leaveRepository;

    public function __construct(CommonRepository $commonRepository, LeaveRepository $leaveRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->leaveRepository  = $leaveRepository;
    }

    public function index()
    {
        $results = PaidLeaveApplication::with(['employee',  'approveBy', 'rejectBy'])
            ->where('employee_id', (session('logged_session_data.employee_id')))
            ->orderBy('paid_leave_application_id', 'desc')
            ->paginate(10);
        return view('admin.leave.applyForPaidLeave.index', ['results' => $results]);
    }

   

    public function create()
    {
        $leaveTypeList   = $this->commonRepository->paidLeaveTypeList();
        $getEmployeeInfo = $this->commonRepository->getEmployeeInfo(Auth::user()->user_id);

        $leaveType             = LeaveType::sum('num_of_day');
        $totalPaidLeaveTaken   = PaidLeaveApplication::where('employee_id', Auth::user()->user_id)->where('status', 2)
        ->where('created_at', Carbon::now()->year)->pluck('number_of_day');
        $totalLeaveTaken       = LeaveApplication::where('employee_id', Auth::user()->user_id)->where('created_at', Carbon::now()->year)
        ->where('status', 2)->pluck('number_of_day');
        $sumOfLeaveTaken       = $totalLeaveTaken->sum() + $totalPaidLeaveTaken->sum();
        $permissableLeave      = $leaveType;
        $checkLeaveEligibility = $sumOfLeaveTaken <= $permissableLeave;
        $leaveBalance          = $leaveType - $sumOfLeaveTaken;

        $data = [
            'checkLeaveEligibility' => $checkLeaveEligibility == true ? 'Eligibile' : 'Not Eligibile',
            'leaveType'             => $leaveType,
            'sumOfLeaveTaken'       => $sumOfLeaveTaken,
            'leaveBalance'          => $leaveBalance,
            'leaveTypeList'         => $leaveTypeList,
            'permissableLeave'      => $permissableLeave,
        ];

        return view('admin.leave.applyForPaidLeave.leave_application_form', ['getEmployeeInfo' => $getEmployeeInfo, 'data' => $data, 'leaveTypeList' => $leaveTypeList]);
    }

    public function getEmployeeLeaveBalance(Request $request)
    {
        $leave_type_id = $request->leave_type_id;
        $employee_id   = $request->employee_id;
        if ($leave_type_id != '' && $employee_id != '') {
            return $this->leaveRepository->calculateEmployeeLeaveBalance($leave_type_id, $employee_id);
        }
    }

    public function applyForTotalNumberOfDays(Request $request)
    {
        $application_from_date = dateConvertFormtoDB($request->application_from_date);
        $application_to_date   = dateConvertFormtoDB($request->application_to_date);
        return $this->leaveRepository->calculateTotalNumberOfLeaveDays($application_from_date, $application_to_date);
    }

    
    public function store(ApplyForPaidLeaveRequest $request)
    {
        $input                          = $request->all();
        $input['application_from_date'] = dateConvertFormtoDB($request->application_from_date);
        $input['application_to_date']   = dateConvertFormtoDB($request->application_to_date);
        $input['application_date']      = date('Y-m-d');
        try {
            PaidLeaveApplication::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return redirect('applyForPaidLeave')->with('success', 'Leave application successfully send.');
        } else {
            return redirect('applyForPaidLeave')->with('error', 'Something error found !, Please try again.');
        }
    }

}
