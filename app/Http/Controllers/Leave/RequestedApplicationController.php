<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Model\Employee;
use App\Model\LeaveApplication;
use App\Model\LeavePermission;
use App\Repositories\LeaveRepository;
use Illuminate\Http\Request;

class RequestedApplicationController extends Controller
{

    protected $leaveRepository;

    public function __construct(LeaveRepository $leaveRepository)
    {
        $this->leaveRepository = $leaveRepository;
    }

    public function index()
    {

        $results = [];

        $isAuthorizedPerson = Employee::where('employee_id', (session('logged_session_data.employee_id')))
            ->whereHas('userName', function ($q) {
                return $q->whereIn('role_id', [1, 2, 3]);
            })->exists();

        $isHod = Employee::where('employee_id', (session('logged_session_data.employee_id')))
            ->whereHas('userName', function ($q) {
                return $q->where('role_id', 4);
            })->exists();

        $departmentWiseEmployee = Employee::select('employee_id')
            ->where('department_id', (session('logged_session_data.department_id')))
            ->get()->toArray();

        $totalEmployee = Employee::select('employee_id')->get()->toArray();

        $hasSupervisorWiseEmployee = Employee::select('employee_id')
            ->where('supervisor_id', (session('logged_session_data.employee_id')))
            ->get()->toArray();

        if ($isAuthorizedPerson) {
            $results = LeaveApplication::with(['employee', 'leaveType'])
                ->whereIn('employee_id', array_values($totalEmployee))
                ->orderBy('status', 'asc')
                ->orderBy('leave_application_id', 'desc')
                ->get();

        } elseif ($isHod) {
            $results = LeaveApplication::with(['employee', 'leaveType'])
                ->whereIn('employee_id', array_values($departmentWiseEmployee))
                ->orderBy('status', 'asc')
                ->orderBy('leave_application_id', 'desc')
                ->get();

        } elseif (count($hasSupervisorWiseEmployee) > 0) {
            $results = LeaveApplication::with(['employee', 'leaveType'])
                ->whereIn('employee_id', array_values($hasSupervisorWiseEmployee))
                ->orderBy('status', 'asc')
                ->orderBy('leave_application_id', 'desc')
                ->get();
        }

        return view('admin.leave.leaveApplication.leaveApplicationList', ['results' => $results]);
    }

    public function viewDetails($id)
    {
        $leaveApplicationData = LeaveApplication::with(['employee' => function ($q) {
            $q->with(['designation']);
        }])->with('leaveType')->where('leave_application_id', $id)->where('status', 1)->first();

        if (!$leaveApplicationData) {
            return response()->view('errors.404', [], 404);
        }
        

        $leaveBalanceArr = $this->leaveRepository->calculateEmployeeLeaveBalanceArray($leaveApplicationData->leave_type_id, $leaveApplicationData->employee_id);

        return view('admin.leave.leaveApplication.leaveDetails', ['leaveApplicationData' => $leaveApplicationData, 'leaveBalanceArr' => $leaveBalanceArr]);
    }

    public function update(Request $request, $id)
    {

        $data = LeaveApplication::findOrFail($id);
        $input = $request->all();
        if ($request->status == 2) {
            $input['approve_date'] = date('Y-m-d');
            $input['approve_by'] = (session('logged_session_data.employee_id'));
        } else {
            $input['reject_date'] = date('Y-m-d');
            $input['reject_by'] = (session('logged_session_data.employee_id'));
        }

        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }
        if ($bug == 0) {
            if ($request->status == 2) {
                return redirect('requestedApplication')->with('success', 'Leave application approved successfully. ');
            } else {
                return redirect('requestedApplication')->with('success', 'Leave application reject successfully. ');
            }
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function approveOrRejectLeaveApplication(Request $request)
    {

        $data = LeaveApplication::findOrFail($request->leave_application_id);
        $input = $request->all();

        if ($request->status == 2) {
            $input['approve_date'] = date('Y-m-d');
            $input['approve_by'] = (session('logged_session_data.employee_id'));
            $input['remarks'] = $request->remark;
        } else {
            $input['reject_date'] = date('Y-m-d');
            $input['reject_by'] = (session('logged_session_data.employee_id'));
            $input['remarks'] = $request->remark;
        }

        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }
        if ($bug == 0) {
            if ($request->status == 2) {
                echo "approve";
            } else {
                echo "reject";
            }
        } else {
            echo "error";
        }
    }

    public function approveOrRejectLeavePermissionByDepartmentHead(Request $request){

        $data = LeavePermission::findOrFail($request->leave_permission_id);
        $input = $request->all();
        $Year  = date("Y",strtotime($data->leave_permission_date));
        $Month = (int)date("m",strtotime($data->leave_permission_date));
        $checkpermissions = LeavePermission::whereMonth('leave_permission_date','=',$Month)->whereYear('leave_permission_date','=',$Year)
        ->where('department_approval_status','1')->where('employee_id',$data->employee_id)->where('status',1)->count();
    // dd($checkpermissions);
       if($checkpermissions < 5){
    
        if($request->status == 1) {            
            $input['department_approval_status'] = 1; 
            $input['head_remarks'] = $request->leave_remark;            
        }else{             
            $input['department_approval_status'] = 2;  
            $input['status'] = 2;  
            $input['head_remarks']= $request->leave_remark;  
        }
    
        try{
            $data->update($input);
            $bug = 0;
        }
        catch(\Exception $e){
            $bug = 1;
        }
        if($bug==0){
            if($request->status == 1) {
                echo "approve";                  
            }else{
                echo "reject";
                        if($data->head_remarks){
                        $reason =" with a remark-".$data->head_remarks;
                    } else{
                            $reason =' ';               
                    } 
                 
            }
        }else {
           echo "error";
        }
        }else {
            if($request->status == 1) {
            $input['department_approval_status'] = 2;  
            $input['status'] = 2;  
            $input['head_remarks'] = 'Permission Limit Exceeds'; 
            $data->update($input); 
            echo "exceeds";
    
            }elseif($request->status == 2){             
                $input['department_approval_status'] = 2;  
                $input['status'] = 2;  
                $input['head_remarks'] = $request->leave_remark;                 
                echo "reject";
                    
            }
         }
    }
    
}
