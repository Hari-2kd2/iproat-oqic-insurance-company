<?php


namespace App\Http\Controllers\Api;

use DateTime;
use Carbon\Carbon;
use App\Model\Employee;
use App\Model\LeaveType;
use App\Model\WorkShift;
use App\Components\Common;
use App\Model\PaidLeaveRule;
use Illuminate\Http\Request;
use App\Model\LeaveApplication;
use App\Model\ShiftChangeRequest;
use Illuminate\Support\Facades\DB;
use App\Model\PaidLeaveApplication;
use App\Http\Controllers\Controller;
use App\Mail\LeaveApplicationMail;
use Illuminate\Support\Facades\Mail;
use App\Repositories\LeaveRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Repositories\CommonRepository;
use App\Http\Requests\ApplyForLeaveRequest;

class ShiftChangeRequestController extends Controller
{
    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }

    public function index(Request $request)
    {

        $results = ShiftChangeRequest::with(['employee', 'approveBy', 'rejectBy'])
            ->where('employee_id', $request->employee_id)
            ->orderBy('shift_change_request_id', 'desc')
            ->get();
        if ($results) {
            return response()->json([
                'message' => 'Shift Change Application Data Successfully Received',
                'data'    => $results,
                'status' => true,
            ], 200);
        } else {
            return response()->json([
                'message' => 'No Data Found',
                'status' => false,
            ], 200);
        }
    }

    public function create(Request $request)
    {
        $workshift = WorkShift::where('branch_id', $request->branch_id)->get();

        return response()->json([
            'data'    => $workshift,
            'status' => true,
        ], 200);
    }

    public function store(Request $request)
    {
        $employee = Employee::where('employee_id', $request->employee_id)->first();
        $input = $request->all();
        $input['application_from_date'] = dateConvertFormtoDB($request->application_from_date);
        $input['application_to_date'] = dateConvertFormtoDB($request->application_to_date);
        $input['regular_shift'] = $request->regular_shift;
        $input['work_shift_id'] =  $request->work_shift_id;
        $input['purpose'] = $request->purpose;
        $input['application_date'] = date('Y-m-d');
        $input['branch_id'] = $employee->branch_id;


        // DB::beginTransaction();

        if ($employee->supervisor_id == '') {
            return response()->json([
                'message' => 'Department Head Data Not Given',
                'status' => false,
            ], 200);
        } elseif ($request->application_from_date == '') {
            return response()->json([
                'message' => 'Application From Date Not Given',
                'status' => false,
            ], 200);
        } elseif ($request->application_to_date == '') {
            return response()->json([
                'message' => 'Application To Date Not Given',
                'status' => false,
            ], 200);
        } elseif ($request->regular_shift == '') {
            return response()->json([
                'message' => 'Regular Shift Name Not Given',
                'status' => false,
            ], 200);
        } elseif ($request->work_shift_id == '') {
            return response()->json([
                'message' => 'Work Shift Name Not Given',
                'status' => false,
            ], 200);
        } elseif ($request->purpose == '') {
            return response()->json([
                'message' => 'Purpose Not Given',
                'status' => false,
            ], 200);
        } else {
            $checkShiftChange = ShiftChangeRequest::where('application_from_date', '>=', $input['application_from_date'])->where('application_to_date', '<=', $input['application_to_date'])
                ->where('employee_id', $employee->employee_id)->where('status', '!=', 3)->first();
            $hod = Employee::where('employee_id', $employee->supervisor_id)->first();

            if (!$checkShiftChange) {
                try {
                    $data = ShiftChangeRequest::create($input);
                    if ($hod != '') {
                        if ($hod->email) {
                            $maildata = Common::mail('emails/mail', $hod->email, 'Shift Change Request Notification', ['head_name' => $hod->first_name . ' ' . $hod->last_name, 'request_info' => $employee->first_name . ' ' . $employee->last_name . ', have requested for shift change (Purpose: ' . $request->purpose . ') from ' . ' ' . dateConvertFormtoDB($request->application_from_date) . ' to ' . dateConvertFormtoDB($request->application_to_date), 'status_info' => '']);
                        }
                    }
                    return response()->json([
                        'message' => 'Shift change application successfully sent.',
                        'status' => true,

                    ], 200);
                } catch (\Exception $e) {
                    //DB::rollback();
                    return response()->json([
                        'message' => 'Something error found !, Please try again.',
                        'status' => false,
                    ], 200);
                }
            } else {
                return response()->json([
                    'message' => 'Application Already Exist.',
                    'status' => true,

                ], 200);
            }
        }
    }
}
