<?php

namespace App\Http\Controllers\Leave;

use App\Model\Employee;
use App\Components\Common;
use Illuminate\Http\Request;
use App\Model\ShiftChangeRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CommonRepository;

class ShiftChangeRequestController extends Controller
{
    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }

    public function index()
    {
        $results = ShiftChangeRequest::with(['employee', 'approveBy', 'rejectBy'])
            ->where('employee_id', (session('logged_session_data.employee_id')))
            ->orderBy('shift_change_request_id', 'desc')
            ->paginate(10);

        return view('admin.leave.applyForShiftChange.index', ['results' => $results]);
    }

    public function create()
    {
        $shiftList = $this->commonRepository->shiftList();
        $getEmployeeInfo = $this->commonRepository->getEmployeeInfo(Auth::user()->user_id);
        return view('admin.leave.applyForShiftChange.shift_change_form', ['getEmployeeInfo' => $getEmployeeInfo, 'shiftList' => $shiftList]);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $input['application_from_date'] = dateConvertFormtoDB($request->application_from_date);
        $input['application_to_date'] = dateConvertFormtoDB($request->application_to_date);
        $input['application_date'] = date('Y-m-d');
        $input['branch_id'] = auth()->user()->branch_id;

        try {

            $checkShiftChange = ShiftChangeRequest::where('application_from_date', '>=',  $input['application_from_date'])->where('application_to_date', '<=', $input['application_to_date'])
                ->where('employee_id', $input['employee_id'])->where('status', '!=', 3)->first();

            $employee = Employee::where('employee_id', session('logged_session_data.employee_id'))->first();
            $hod = Employee::where('employee_id', $employee->supervisor_id)->first();

            if (!$checkShiftChange) {
                $data = ShiftChangeRequest::create($input);

                if ($hod->email) {
                    $maildata = Common::mail('emails/mail', $hod->email, 'Shift Change Request Notification', ['head_name' => $hod->first_name . ' ' . $hod->last_name, 'request_info' => $employee->first_name . ' ' . $employee->last_name . ', have requested for shift change (Purpose: ' . $request->purpose . ') from ' . ' ' . dateConvertFormtoDB($request->application_from_date) . ' to ' . dateConvertFormtoDB($request->application_to_date), 'status_info' => '']);
                }
                return redirect('applyForShiftChange')->with('success', 'Shift change application successfully send.');
            } else {
                return redirect('applyForShiftChange')->with('error', 'Shift change application already exists.');
            }
            DB::commit();
        } catch (\Exception $e) {
            return redirect('applyForShiftChange')->with('error', 'Something error found !, Please try again.');
            DB::rollback();
        }
    }
}
