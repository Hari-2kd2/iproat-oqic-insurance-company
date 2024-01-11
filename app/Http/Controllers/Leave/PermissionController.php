<?php

namespace App\Http\Controllers\Leave;

use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CommonRepository;

class PermissionController extends Controller
{
    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }

    public function index()
    {
        $results = Permission::with(['employee', 'approveBy', 'rejectBy'])
            ->where('employee_id', (session('logged_session_data.employee_id')))
            ->orderBy('on_duty_id', 'desc')
            ->paginate(10);

        return view('admin.leave.OnDuty.index', ['results' => $results]);
    }

    public function create()
    {
        $getEmployeeInfo = $this->commonRepository->getEmployeeInfo(Auth::user()->user_id);
        return view('admin.leave.OnDuty.on_duty_form', ['getEmployeeInfo' => $getEmployeeInfo]);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $input['application_from_date'] = dateConvertFormtoDB($request->application_from_date);
        $input['application_to_date'] = dateConvertFormtoDB($request->application_to_date);
        $input['application_date'] = date('Y-m-d');

        try {
            $data = Permission::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect('OnDuty')->with('success', 'On Duty application successfully send.');
        } else {
            return redirect('OnDuty')->with('error', 'Something error found !, Please try again.');
        }
    }
}
