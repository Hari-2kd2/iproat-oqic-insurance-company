<?php

namespace App\Http\Controllers\Api;

use App\User;
use Carbon\Carbon;
use App\Model\Employee;
use Illuminate\Http\Request;
use App\Model\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Repositories\EmployeeRepository;

class EmployeeController extends Controller
{


    public function add(Request $request)
    {
        Log::info($request->all());
        DB::beginTransaction();
        $emp_repositories = new EmployeeRepository;
        $employeeAccountDataFormat = $emp_repositories->makeEmployeeAccountDataFormat($request->all());
        $parentData               = User::create($employeeAccountDataFormat);
        Log::info($parentData);
        User::where('user_id', $parentData->user_id)->update(['user_id' => $request->user_id]);

        $employeeData = $request->all();
        $employeeData['user_id'] = $parentData->user_id;
        $childData               = Employee::create($employeeData);
        Log::info($childData);

        Employee::where('employee_id', $childData->employee_id)->update(['employee_id' => $request->employee_id]);

        DB::commit();

        return json_encode(['status' => 'success', 'message' => 'Employee created Successfully !'], 200);
    }


    public function update(Request $request)
    {

        DB::beginTransaction();
        $employee = Employee::findOrFail($request->employee_id);

        $emp_repositories = new EmployeeRepository;
        $employeeDataFormat = $emp_repositories->makeEmployeePersonalInformationDataFormat($request->all());
        $employee->update($employeeDataFormat);

        $employeeAccountDataFormat = $emp_repositories->makeEmployeeAccountDataFormat($request->all(), 'update');
        User::where('user_id', $employee->user_id)->update($employeeAccountDataFormat);
        DB::commit();
        Log::info($employeeDataFormat);

        return json_encode(['status' => 'success', 'message' => 'Successfully updated !', 'data' => $employeeAccountDataFormat], 200);
    }

    public function destroy(Request $request)
    {

        $employee = Employee::FindOrFail($request->id);
        $result = $employee->delete();
        if ($result) {
            DB::table('user')->where('user_id', $employee->user_id)->update(['deleted_at' => Carbon::now()]);
            DB::table('employee_access_control')->where('employee', $request->id)->delete();
            DB::table('ms_sql')->where('employee', $request->id)->delete();
        }
    }
}
