<?php

namespace App\Imports;

use App\Model\ApproveOverTime;
use App\Model\EmployeeInOutData;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ApprovedOvertimeImport implements ToModel, WithValidation, WithStartRow, WithLimit
{
    use Importable;

    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function rules(): array
    {
        return [
            '*.0' => 'required',
            '*.1' => 'required|regex:/^\S*$/u',
            '*.2' => 'required|date',
            '*.3' => 'required',
            '*.4' => 'required',
            '*.5' => 'nullable',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'Sr.No is required',
            '1.required' => 'Employee ID is required',
            '2.required' => 'Date Field is Required ',
            '3.required' => 'Actual Overtime field is required',
            '4.required' => 'Approved Overtime field is required',
            '1.regex' => 'Space not allowed in Employee ID',
        ];
    }

    public function model(array $row)
    {
        $dataUpdate = false;
        $dataInsert = false;
        $approveStatus = 0;

        $checkApprovedOt = ApproveOverTime::where('finger_print_id', $row[1])->first();

        if ($checkApprovedOt) {
            $dataUpdate = true;
        } else {
            $dataInsert = true;
        }

        $date = "0000-00-00";

        if ($row[2]) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[2])->format('Y-m-d');
            } catch (\Throwable $th) {
                $date = date('Y-m-d', strtotime($row[2]));
            }
        }

        $isValid = EmployeeInOutData::where('date', $date)->where('finger_print_id', $row[1])->where('over_time', $row[3])->exists();

        if ($isValid) {
            
            if ($dataInsert) {

                $approveOvertime = ApproveOverTime::create([
                    'finger_print_id' => $row[1],
                    'date' => $date,
                    'actual_overtime' => $row[3],
                    'approved_overtime' => $row[4],
                    'remark' => $row[5],
                    'status' => $approveStatus,
                    'created_by' => auth()->user()->user_id,
                    'updated_by' => auth()->user()->user_id,
                ]);

            $employeeInOutData = EmployeeInOutData::where('finger_print_id', $approveOvertime->finger_print_id)->where('date', $approveOvertime->date)->update(['approve_over_time_id' => $approveOvertime->approve_over_time_id]);

                
            }
            
            if ($dataUpdate) {


                $approveOvertime = ApproveOverTime::where('finger_print_id', $row[1])->where('date', $date)->first();
                
                $approveOvertime->update([
                    'finger_print_id' => $row[1],
                    'date' => $date,
                    'actual_overtime' => $row[3],
                    'approved_overtime' => $row[4],
                    'remark' => $row[5],
                    'status' => $approveStatus,
                    'created_by' => auth()->user()->user_id,
                    'updated_by' => auth()->user()->user_id,
                ]);

                $approveOvertime->save();

            $employeeInOutData = EmployeeInOutData::where('finger_print_id', $row[1])->where('date', $date)->update(['approve_over_time_id' => $approveOvertime->approve_over_time_id]);

            }
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
