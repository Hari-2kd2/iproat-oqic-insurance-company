<?php

namespace App\Repositories;

use App\Lib\Enumerations\UserStatus;
use App\Model\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeRepository
{

    public function incentive()
    {
        $results = ['Not Applicable', 'Applicable'];
        $options = ['' => '---- Please select ----'];
        foreach ($results as $key => $value) {
            $options[$key] = $value;
        }
        return $options;
    }
    public function salaryLimit()
    {
        $results = ['< 20000', '> 20000'];
        $options = ['' => '---- Please select ----'];
        foreach ($results as $key => $value) {
            $options[$key] = $value;
        }
        return $options;
    }
    public function workShift()
    {
        $results = ['General', 'Rotational'];
        $options = ['' => '---- Please select ----'];
        foreach ($results as $key => $value) {
            $options[$key] = $value;
        }
        return $options;
    }

    public function makeEmployeeAccountDataFormat($data, $action = false)
    {

        $employeeAccountData['role_id'] = $data['role_id'];

        if ($action != 'update') {
            if ($data['password']) {
                $employeeAccountData['password'] = Hash::make($data['password']);
            } else {
                $employeeAccountData['password'] = Hash::make('demo1234');
            }
        }

        $employeeAccountData['user_name'] = $data['user_name'];

        $employeeAccountData['email'] = $data['email'];

        $employeeAccountData['status'] = $data['status'];

        $employeeData['branch_id'] = $data['branch_id'];

        $employeeAccountData['created_by'] = 1;

        $employeeAccountData['updated_by'] = 1;

        return $employeeAccountData;
    }

    public function makeEmployeePersonalInformationDataFormat($data)
    {

        $employeeData['first_name'] = $data['first_name'];

        $employeeData['last_name'] = $data['last_name'];

        $employeeData['nationality'] = $data['nationality'];

        $employeeData['finger_id'] = $data['finger_id'];

        $employeeData['document_title'] = $data['document_title'];

        $employeeData['document_expiry'] = $data['document_expiry'];
        if (isset($data['document_file'])) {
            $employeeData['document_name'] = date('Y_m_d_H_i_s') . '_' . $data['document_file']->getClientOriginalName();
        }

        $employeeData['document_title2'] = $data['document_title2'];

        $employeeData['document_expiry2'] = $data['document_expiry2'];
        if (isset($data['document_file2'])) {
            $employeeData['document_name2'] = date('Y_m_d_H_i_s') . '_' . $data['document_file2']->getClientOriginalName();
        }

        $employeeData['document_title3'] = $data['document_title3'];

        $employeeData['document_expiry3'] = $data['document_expiry3'];
        if (isset($data['document_file3'])) {
            $employeeData['document_name3'] = date('Y_m_d_H_i_s') . '_' . $data['document_file3']->getClientOriginalName();
        }

        $employeeData['document_title4'] = $data['document_title4'];

        $employeeData['document_expiry4'] = $data['document_expiry4'];
        if (isset($data['document_file4'])) {
            $employeeData['document_name4'] = date('Y_m_d_H_i_s') . '_' . $data['document_file4']->getClientOriginalName();
        }

        $employeeData['document_title5'] = $data['document_title5'];

        $employeeData['document_expiry5'] = $data['document_expiry5'];
        if (isset($data['document_file5'])) {
            $employeeData['document_name5'] = date('Y_m_d_H_i_s') . '_' . $data['document_file5']->getClientOriginalName();
        }

        $employeeData['department_id'] = $data['department_id'];

        $employeeData['designation_id'] = $data['designation_id'];

        $employeeData['branch_id'] = $data['branch_id'];

        $employeeData['supervisor_id'] = $data['supervisor_id'];

        // $employeeData['work_shift_id'] = $data['work_shift_id'];
        $employeeData['work_shift'] = $data['work_shift'];

        // $employeeData['esi_card_number'] = $data['esi_card_number'];

        // $employeeData['pf_account_number'] = $data['pf_account_number'];

        $employeeData['pay_grade_id'] = $data['pay_grade_id'];

        $employeeData['hourly_salaries_id'] = $data['hourly_salaries_id'];

        $employeeData['email'] = $data['email'];

        $employeeData['date_of_birth'] = dateConvertFormtoDB($data['date_of_birth']);

        $employeeData['date_of_joining'] = dateConvertFormtoDB($data['date_of_joining']);

        $employeeData['date_of_leaving'] = dateConvertFormtoDB($data['date_of_leaving']);

        $employeeData['marital_status'] = $data['marital_status'];

        $employeeData['address'] = $data['address'];

        $employeeData['emergency_contacts'] = $data['emergency_contacts'];

        $employeeData['gender'] = $data['gender'];

        $employeeData['religion'] = $data['religion'];
        $employeeData['incentive'] = $data['incentive'];
        $employeeData['salary_limit'] = $data['salary_limit'];

        $employeeData['phone'] = $data['phone'];

        $employeeData['status'] = $data['status'];

        $employeeData['created_by'] = 1;

        $employeeData['updated_by'] = 1;

        return $employeeData;
    }

    public function makeEmployeeEducationDataFormat($data, $employee_id, $action = false)
    {

        $educationData = [];

        if (isset($data['institute'])) {

            for ($i = 0; $i < count($data['institute']); $i++) {

                $educationData[$i] = [

                    'employee_id' => $employee_id,

                    'institute' => $data['institute'][$i],

                    'board_university' => $data['board_university'][$i],

                    'degree' => $data['degree'][$i],

                    'passing_year' => $data['passing_year'][$i],

                    'result' => $data['result'][$i],

                    'cgpa' => $data['cgpa'][$i],

                ];

                if ($action == 'update') {

                    $educationData[$i]['educationQualification_cid'] = $data['educationQualification_cid'][$i];
                }
            }
        }

        return $educationData;
    }

    public function makeEmployeeExperienceDataFormat($data, $employee_id, $action = false)
    {

        $experienceData = [];

        if (isset($data['organization_name'])) {

            for ($i = 0; $i < count($data['organization_name']); $i++) {

                $experienceData[$i] = [

                    'employee_id' => $employee_id,

                    'organization_name' => $data['organization_name'][$i],

                    'designation' => $data['designation'][$i],

                    'from_date' => dateConvertFormtoDB($data['from_date'][$i]),

                    'to_date' => dateConvertFormtoDB($data['to_date'][$i]),

                    'responsibility' => $data['responsibility'][$i],

                    'skill' => $data['skill'][$i],

                ];

                if ($action == 'update') {

                    $experienceData[$i]['employeeExperience_cid'] = $data['employeeExperience_cid'][$i];
                }
            }
        }

        return $experienceData;
    }

    public function bonusDayEligibility()
    {

        $employees = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'), 'designation_name', 'department_name', 'date_of_joining', 'date_of_leaving', 'finger_id', 'employee_id', 'branch_name')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('department', 'department.department_id', '=', 'employee.department_id')
            ->join('branch', 'branch.branch_id', '=', 'employee.branch_id')
            ->where('status', UserStatus::$ACTIVE)->where("date_of_joining", "<=", Carbon::now()->subMonths(24))->orderBy('date_of_joining', 'asc')->get();
        $dataFormat = [];
        $tempArray = [];
        if (count($employees) > 0) {
            foreach ($employees as $employee) {
                $tempArray['date_of_joining'] = $employee->date_of_joining;
                $tempArray['date_of_leaving'] = $employee->date_of_leaving;
                $tempArray['employee_id'] = $employee->employee_id;
                $tempArray['designation_name'] = $employee->designation_name;
                $tempArray['fullName'] = $employee->fullName;
                $tempArray['phone'] = $employee->phone;
                $tempArray['finger_id'] = $employee->finger_id;
                $tempArray['department_name'] = $employee->department_name;
                $tempArray['branch_name'] = $employee->branch_name;

                $dataFormat[$employee->employee_id][] = $tempArray;
            }
        } else {
            $tempArray['status'] = 'No Data Found';
            $dataFormat[] = $tempArray['status'];
        }
        return $dataFormat;
    }
}
