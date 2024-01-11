<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\UserStatus;
use App\Model\Department;
use App\Model\Designation;
use App\Model\Employee;
use App\MOdel\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BonusDayController extends Controller
{

    public function index(Request $request)
    {
        $departmentList  = Department::get();
        $designationList = Designation::get();
        $roleList        = Role::get();

        $results = Employee::with(['userName' => function ($q) {
            $q->with('role');
        }, 'department', 'designation', 'branch', 'payGrade', 'supervisor', 'hourlySalaries'])
            ->orderBy('date_of_joining', 'ASC')->where('status', UserStatus::$ACTIVE)->where('permanent_status',UserStatus::$PERMANENT)->where("date_of_joining", "<=", Carbon::now()->subMonths(24))->orderBy('date_of_joining', 'asc')->paginate(10);

        if (request()->ajax()) {
            if ($request->role_id != '') {
                $results = Employee::whereHas('userName', function ($q) use ($request) {
                    $q->with('role')->where('role_id', $request->role_id);
                })->with('department', 'designation', 'branch', 'payGrade')->where('status', UserStatus::$ACTIVE)->where('permanent_status',UserStatus::$PERMANENT)->where("date_of_joining", "<=", Carbon::now()->subMonths(24))->orderBy('date_of_joining', 'ASC');
            } else {
                $results = Employee::with(['userName' => function ($q) {
                    $q->with('role');
                }, 'department', 'designation', 'branch', 'payGrade'])->where('status', UserStatus::$ACTIVE)->where('permanent_status',UserStatus::$PERMANENT)->where("date_of_joining", "<=", Carbon::now()->subMonths(24))->orderBy('date_of_joining', 'ASC');
            }

            if ($request->department_id != '') {
                $results->where('department_id', $request->department_id);
            }

            if ($request->designation_id != '') {
                $results->where('designation_id', $request->designation_id);
            }

            if ($request->employee_name != '') {
                $results->where(function ($query) use ($request) {
                    $query->where('first_name', 'like', '%' . $request->employee_name . '%')
                        ->orWhere('last_name', 'like', '%' . $request->employee_name . '%');
                });
            }

            $results = $results->paginate(10);
            return View('admin.payroll.bonusday.pagination', compact('results'))->render();
        }

        return view('admin.payroll.bonusday.index', ['results' => $results, 'departmentList' => $departmentList, 'designationList' => $designationList, 'roleList' => $roleList]);

    }

}
