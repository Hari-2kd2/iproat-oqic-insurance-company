<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Model\Employee;
use App\Model\LeaveApplication;
use App\Model\OnDuty;
use App\Model\ShiftChangeRequest;
use App\Repositories\LeaveRepository;
use Illuminate\Http\Request;

class RequestedShiftChangeRequestController extends Controller
{

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
            $results = ShiftChangeRequest::with(['employee'])
                ->whereIn('employee_id', array_values($totalEmployee))
                ->orderBy('status', 'asc')
                ->orderBy('shift_change_request_id', 'desc')
                ->get();
        } elseif ($isHod) {
            $results = ShiftChangeRequest::with(['employee'])
                ->whereIn('employee_id', array_values($departmentWiseEmployee))
                ->orderBy('status', 'asc')
                ->orderBy('shift_change_request_id', 'desc')
                ->get();
        } elseif (count($hasSupervisorWiseEmployee) > 0) {
            $results = ShiftChangeRequest::with(['employee'])
                ->whereIn('employee_id', array_values($hasSupervisorWiseEmployee))
                ->orderBy('status', 'asc')
                ->orderBy('shift_change_request_id', 'desc')
                ->get();
        }

        return view('admin.leave.shiftChangeRequest.shiftChangeRequestList', ['results' => $results]);
    }

    public function viewDetails($id)
    {
        $shiftChangeRequestData  = ShiftChangeRequest::with(['employee' => function ($q) {
            $q->with(['designation']);
        }])->where('shift_change_request_id', $id)->where('status', 1)->first();

        if (!$shiftChangeRequestData ) {
            return response()->view('errors.404', [], 404);
        }

        return view('admin.leave.shiftChangeRequest.shiftChangeRequestDetails', ['shiftChangeRequestData' => $shiftChangeRequestData ]);
    }

    public function update(Request $request, $id)
    {

        $data = ShiftChangeRequest::findOrFail($id);
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
                return redirect('requestedShiftChangeRequest')->with('success', 'Shift request approved successfully. ');
            } else { 
                return redirect('requestedShiftChangeRequest')->with('success', 'Shift request reject successfully. ');
            }
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function approveOrRejectOnDutyApplication(Request $request)
    {

        $data = ShiftChangeRequest::findOrFail($request->shift_change_request_id);
        $input = $request->all();

        if ($request->status == 2) {
            $input['status'] = 2;
            $input['approve_date'] = date('Y-m-d');
            $input['approve_by'] = (session('logged_session_data.employee_id'));
            $input['remarks']    = $request->head_remark; 
        } else {
            $input['status'] = 3;
            $input['reject_date'] = date('Y-m-d');
            $input['reject_by'] = (session('logged_session_data.employee_id'));
            $input['remarks']    = $request->head_remark; 
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
    public function approveOrRejectShiftChangeApplication(Request $request)
    {

        $data = ShiftChangeRequest::findOrFail($request->shift_change_request_id);
        $input = $request->all();

        if ($request->status == 2) {
            $input['status'] = 2;
            $input['approve_date'] = date('Y-m-d');
            $input['approve_by'] = (session('logged_session_data.employee_id'));
        } else {
            $input['status'] = 3;
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
                echo "approve";
            } else {
                echo "reject";
            }
        } else {
            echo "error";
        }
    }
}
