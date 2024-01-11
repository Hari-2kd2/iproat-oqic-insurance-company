<?php

namespace App\Imports;

use App\Lib\Enumerations\UserStatus;
use App\Model\Branch;
use App\Model\Department;
use App\Model\Designation;
use App\Model\Employee;
use App\Model\Role;
use App\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeeImport implements ToModel, WithValidation, WithStartRow, WithLimit
{
    use Importable;

    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function sanitize()
    {
        $this->data['*.21'] = trim($this->data['*.21']);
        dd($this->data);
    }

    public function rules(): array
    {
        return [
            '*.0' => 'required',
            '*.1' => 'required|regex:/^\S*$/u',
            '*.2' => 'required|exists:role,role_name',
            '*.3' => 'required',
            '*.4' => 'required|exists:department,department_name',
            '*.5' => 'required|exists:designation,designation_name',
            '*.6' => 'required|exists:branch,branch_name',
            '*.7' => 'nullable|exists:user,user_name',
            '*.8' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            '*.9' => 'nullable|email',
            '*.10' => 'required',
            '*.11' => 'nullable',
            '*.12' => 'nullable',
            '*.13' => 'nullable',
            '*.14' => 'nullable',
            '*.15' => 'nullable',
            '*.16' => 'nullable',
            '*.17' => 'nullable',
            '*.18' => 'nullable',

            '*.19' => 'nullable|in:Yes,No',

        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'Sr.No is required',
            '1.required' => 'User name is required',
            '2.required' => 'Role name should be same as the name provided in Master',
            '3.required' => 'Employee Id is required (ie: Device Unique id) ',
            '4.required' => 'Department Name should be same as the name provided in Master',
            '5.required' => 'Designation Name should be same as the name provided in Master',
            '6.required' => 'Branch Name should be same as the name provided in Master',
            '7.nullable' => 'HOD Name should be same as the  user name provided in Master',
            // '8.nullable' => 'Phone No is required',
            // '8.min' => 'Phone No should be min 10 digits',
            // '8.regex' => 'Phone No is invalid',
            // '9.required' => 'Email is required',
            '10.required' => 'Employee first name is required',
            '11.required' => 'Employee last name is required',
            //  '12.required' => 'Date of birth is required',
            //  '13.required' => 'Date of joining is required',
            //  '14.in' => 'Invalid Gender ,can user only Male/Female/NoDisclosure ',
            //   '15.required' => 'Religion is required',
            // '16.in' => 'Invalid Marital status ,can user only use Married/Unmarried/NoDisclosure',
            //    '17.required' => 'Address is required',
            //   '18.required' => 'Emergency Contact is required',
            // '19.in' => 'Incentive is invalid, it should be IncentiveApplicable/NotApplicable',
            //  '20.in' => 'Salary Limit is invalid, it should be lessthen/morethen20000',
            //  '21.in' => 'WorkShift is invalid, it should be General/Rotational',
            '19.in' => 'Invalid status ,can user only use Yes/No',

            '1.unique' => 'Username should be unique',
            '1.regex' => 'Space not allowed in Username',
            '2.exists' => 'Role name doest not exists',
            '3.unique' => 'Employee Id should be unique',
            '4.exists' => 'Department name doest not exists',
            '5.exists' => 'Designation name doest not exists',
            '6.exists' => 'Contractor name doest not exists',
            '7.exists' => 'HOD user name doest not exists',

        ];
    }

    public function model(array $row)
    {

        // info($row);

        $dataUpdate = false;
        $dataInsert = false;
        $usr_status = UserStatus::$ACTIVE;
        $incentive = 0;
        $salary_limit = 0;
        $work_shift = 0;

        $checkEmployee = Employee::where('finger_id', $row[3])->first();

        if ($checkEmployee) {
            $checkUser = User::where('user_id', $checkEmployee->user_id)->first();
            $dataUpdate = true;
        } else {
            $dataInsert = true;
        }

        $dob = "0000-00-00";
        $doj = "0000-00-00";
        $password = '';

        if ($row[12]) {
            try {
                $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[12])->format('Y-m-d');
                $password = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[12])->format('Ymd');
            } catch (\Throwable $th) {
                $dob = date('Y-m-d', strtotime($row[13]));
                $password = date('Ymd', strtotime($row[13]));
            }
        }

        if ($row[13]) {
            try {
                $doj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[13])->format('Y-m-d');
            } catch (\Throwable $th) {
                $doj = date('Y-m-d', strtotime($row[13]));
            }
        }

        $role = Role::where('role_name', $row[2])->first();
        $dept = Department::where('department_name', $row[4])->first();
        $designation = Designation::where('designation_name', $row[5])->first();

        if (isset($row[7]) && isset($row[8])) {
            $user = User::where('user_name', $row[7])->first();
            $emp = Employee::where('user_id', $user->user_id)->first();
        }

        $branch = Branch::where('branch_name', $row[6])->first();







        if ($row[19] == 'No') {
            $usr_status = UserStatus::$INACTIVE;
        }

        if ($dataInsert) {

            $userData = User::create([
                'user_name' => $row[1],
                'role_id' => $role->role_id,
                'password' => Hash::make('123'),
                'status' => $usr_status,
                'created_by' => auth()->user()->user_id,
                'updated_by' => auth()->user()->user_id,
            ]);

            $employeeData = Employee::create([
                'user_id' => $userData->user_id,
                'finger_id' => $row[3],
                'department_id' => $dept->department_id,
                'designation_id' => $designation->designation_id,
                'branch_id' => $branch->branch_id,
                'supervisor_id' => isset($emp->employee_id) ? $emp->employee_id : null,
                // 'phone' => $row[8],
                //  'email' => $row[9],
                'first_name' => $row[10],
                'last_name' => $row[11],
                'date_of_birth' => $dob,
                'date_of_joining' => $doj,
                'gender' => $row[14],
                'religion' => $row[15],
                'marital_status' => $row[16],
                'address' => $row[17],
                'emergency_contacts' => $row[18],
                'incentive' => $incentive,
                'salary_limit' => $salary_limit,
                'work_shift' => $work_shift,
                'status' => $usr_status,
                'created_by' => auth()->user()->user_id,
                'updated_by' => auth()->user()->user_id,
            ]);
        }

        if ($dataUpdate) {

            $userData = User::where('user_id', $checkUser->user_id)->update([
                'user_name' => $row[1],
                'role_id' => $role->role_id,
                // 'password' => Hash::make($password),
                'status' => $usr_status,
                'created_by' => auth()->user()->user_id,
                'updated_by' => auth()->user()->user_id,
            ]);

            $employeeData = Employee::where('employee_id', $checkEmployee->employee_id)->update([
                'user_id' => $checkUser->user_id,
                'finger_id' => $row[3],
                'department_id' => $dept->department_id,
                'designation_id' => $designation->designation_id,
                'branch_id' => $branch->branch_id,
                'supervisor_id' => isset($emp->employee_id) ? $emp->employee_id : null,
                'phone' => $row[8],
                'email' => $row[9],
                'first_name' => $row[10],
                'last_name' => $row[11],
                'date_of_birth' => $dob,
                'date_of_joining' => $doj,
                'gender' => $row[14],
                'religion' => $row[15],
                'marital_status' => $row[16],
                'address' => $row[17],
                'emergency_contacts' => $row[18],
                //  'incentive' => $incentive,
                //   'salary_limit' => $salary_limit,
                //  'work_shift' => $work_shift,
                'status' => $usr_status,
                'created_by' => auth()->user()->user_id,
                'updated_by' => auth()->user()->user_id,
            ]);
        }
    }

    public function startRow(): int
    {
        return 2;
    }

    public function limit(): int
    {
        return 200;
    }
}
