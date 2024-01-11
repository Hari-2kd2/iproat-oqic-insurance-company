<?php

namespace App\Http\Controllers\Leave;

use App\Model\OnDuty;
use App\Model\Employee;
use App\Components\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CommonRepository;

class OnDutyController extends Controller
{
    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }

    public function index()
    {
        $results = OnDuty::with(['employee', 'approveBy', 'rejectBy'])
            ->where('employee_id', (session('logged_session_data.employee_id')))
            ->orderBy('on_duty_id', 'desc')
            ->paginate(10);

        return view('admin.leave.applyForOnDuty.index', ['results' => $results]);
    }

    public function create()
    {
        $getEmployeeInfo = $this->commonRepository->getEmployeeInfo(Auth::user()->user_id);
        return view('admin.leave.applyForOnDuty.form', ['getEmployeeInfo' => $getEmployeeInfo]);
    }

    public function store(Request $request)
    {
        $employee = Employee::where('employee_id', session('logged_session_data.employee_id'))->first();
        $input = $request->all();
        $input['application_from_date'] = dateConvertFormtoDB($request->application_from_date);
        $input['application_to_date'] = dateConvertFormtoDB($request->application_to_date);
        $input['application_date'] = date('Y-m-d');
        $input['branch_id'] = $employee->branch_id;


        $hod = Employee::where('employee_id', $employee->supervisor_id)->first();

        try {

            $checkOD = OnDuty::where('application_from_date', '>=', $input['application_from_date'])->where('application_to_date', '<=', $input['application_to_date'])
                ->where('employee_id', $input['employee_id'])->where('status', '!=', 3)->first();

            if (!$checkOD) {
                $data = OnDuty::create($input);

                if ($hod->email) {
                    $maildata = Common::mail('emails/mail', $hod->email, 'On Duty Request Notification', ['head_name' => $hod->first_name . ' ' . $hod->last_name, 'request_info' => $employee->first_name . ' ' . $employee->last_name . ', have requested for on duty (Purpose: ' . $request->purpose . ') from ' . ' ' . dateConvertFormtoDB($request->application_from_date) . ' to ' . dateConvertFormtoDB($request->application_to_date), 'status_info' => '']);
                }

                return redirect('applyForOnDuty')->with('success', 'On Duty application successfully send.');
            } else {
                return redirect('applyForOnDuty')->with('error', 'On Duty application already exists.');
            }
        } catch (\Exception $e) {
            return redirect('applyForOnDuty')->with('error', 'Something error found !, Please try again.');
        }
    }
}
