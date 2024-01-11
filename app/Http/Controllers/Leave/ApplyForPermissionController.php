<?php

namespace App\Http\Controllers\Leave;

use Carbon\Carbon;
use App\Model\Employee;
use App\Model\LeaveType;
use App\Components\Common;
use Illuminate\Http\Request;
use App\Model\LeaveConfigure;
use App\Model\LeavePermission;
use App\Model\PaidLeaveApplication;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\LeaveRepository;
use App\Repositories\CommonRepository;
use App\Http\Requests\ApplyForPermissionRequest;

class ApplyForPermissionController extends Controller
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
         
            $results = LeavePermission::with(['employee'])
                ->where('employee_id', session('logged_session_data.employee_id'))
                ->orderBy('leave_permission_date', 'desc')
                ->paginate(10);         
        return view('admin.leave.applyForPermission.index', ['results' => $results]);
    }

    public function create()
    {
        $getEmployeeInfo = $this->commonRepository->getEmployeeInfo(Auth::user()->user_id);
        $employeeList = $this->commonRepository->employeeList();
        $Year  = Carbon::now()->year;
        $Month = DATE('m');
        $takenpermissions = LeavePermission::whereMonth('leave_permission_date', '=', $Month)->whereYear('leave_permission_date', '=', $Year)
            ->where('department_approval_status', '1')->where('employee_id', $getEmployeeInfo->employee_id)
            ->where('status', 1)->count();
        $appliedpermissions = LeavePermission::whereMonth('leave_permission_date', '=', $Month)->whereYear('leave_permission_date', '=', $Year)
            ->where('employee_id', $getEmployeeInfo->employee_id)->count();

        return view('admin.leave.applyForPermission.leave_permission_form', [
            'getEmployeeInfo' => $getEmployeeInfo, 'employeeList' => $employeeList,
            'takenPermissions' => $takenpermissions, 'appliedpermissions' => $appliedpermissions
        ]);
    }



    public function applyForTotalNumberOfPermissions(Request $request)
    {
        $permission_date = dateConvertFormtoDB($request->permission_date);
        $employee_id = $request->employee_id;
        $Year  = date("Y", strtotime($permission_date));
        $Month = (int)date("m", strtotime($permission_date));
        $checkpermissions = LeavePermission::whereMonth('leave_permission_date', '=', $Month)->whereYear('leave_permission_date', '=', $Year)
            ->where('department_approval_status', '1')->where('employee_id', $employee_id)->where('status', 1)->count();

        return $checkpermissions;
    }

    public function store(ApplyForPermissionRequest $request)
    {
        $employee_data = Employee::where('employee_id', $request->employee_id)->first();

        $input                            = $request->all();
        $input['leave_permission_date']   = dateConvertFormtoDB($request->permission_date);
        $input['permission_duration']     = $request->permission_duration;
        $input['leave_permission_purpose'] = $request->purpose;
        $input['department_head']         = $employee_data->supervisor_id;
        $input['from_time']               = $request->from_time;
        $input['to_time']                 = $request->to_time;
        $input['branch_id'] = auth()->user()->branch_id;

        $hod = Employee::where('employee_id', $employee_data->supervisor_id)->first();
        $if_exists = LeavePermission::where('employee_id', $request->employee_id)->where('leave_permission_date', dateConvertFormtoDB($request->permission_date))->first();

        if (!$if_exists) {
            LeavePermission::create($input);

            if ($hod->email) {
                $maildata = Common::mail('emails/mail', $hod->email, 'Permission Request Notification', ['head_name' => $hod->first_name . ' ' . $hod->last_name, 'request_info' => $employee_data->first_name . ' ' . $employee_data->last_name . ', have requested for leave (Purpose: ' . $request->purpose . ') On ' . ' ' . dateConvertFormtoDB($input['leave_permission_date']), 'status_info' => '']);
            }

            $bug = 0;
        } else {
            $bug = 3;
        }


        if ($bug == 0) {
            return redirect('applyForPermission')->with('success', 'Permission Request successfully send.');
        } elseif ($bug == 3) {
            return redirect('applyForPermission')->with('error', 'Permission Request Already Exist');
        } else {
            return redirect('applyForPermission')->with('error', 'Something error found !, Please try again.');
        }
    }

    
    public function permissionrequest()
    {
        $departmentresults = LeavePermission::where('department_head', Auth::user()->user_id)->paginate(10);
        return view('admin.leave.applyForPermission.permission_requests', ['departmentlist' => $departmentresults]);
    }
}
