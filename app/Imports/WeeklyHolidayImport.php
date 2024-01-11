<?php

namespace App\Imports;

use App\Model\Employee;
use App\Model\WeeklyHoliday;
use Illuminate\Support\Facades\Log;
use App\Lib\Enumerations\UserStatus;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class WeeklyHolidayImport  implements ToModel, WithValidation, WithStartRow
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
            '*.0' => 'required|exists:employee,finger_id',
            '*.1' => 'required|date_format:Y-m',
            '*.2' => 'required|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'Employee id is required',
            '1.required' => 'Month is required',
            '2.required' => 'Day name is required',
            '2.required' => 'Day name is required',
            '0.exists' => 'Employee id doest not exists',
            '2.in' => 'Invalid day name, can only Sunday / Monday / Tuesday / Wednesday / Thursday / Friday / Saturday',
            '1.date_format' => 'Date format should be yyyy-mm',
        ];
    }

    public function model(array $row)
    {
        // Log::info($row);


        $employee = Employee::where('finger_id', $row[0])->first();

        $day_name = ucwords($row[2]);

        $month = $row[1];

        $week_days = [];

        $week_days =  $this->findWeekdays($month, $day_name);

        // Log::info($day_name);
        // Log::info('Create/Update');

        $ifExists = WeeklyHoliday::where('employee_id', $employee->employee_id)->where('month',  $row[1])->first();

        if (!$ifExists) {
            // Log::info($week_days);
            $holidayData = WeeklyHoliday::create([
                'employee_id' => $employee->employee_id,
                'month' =>  $row[1],
                'day_name' => ucwords($row[2]),
                'weekoff_days' => \json_encode($week_days),
                'status' => UserStatus::$ACTIVE,
                'created_by' => auth()->user()->user_id,
                'updated_by' => auth()->user()->user_id,
            ]);
        } else {
            // Log::info($week_days);
            $holidayData =  WeeklyHoliday::where('employee_id', $employee->employee_id)->where('month',  $row[1])->update([
                'employee_id' => $employee->employee_id,
                'month' =>  $row[1],
                'day_name' => ucwords($row[2]),
                'weekoff_days' => \json_encode($week_days),
                'status' => UserStatus::$ACTIVE,
                'created_by' => auth()->user()->user_id,
                'updated_by' => auth()->user()->user_id,
            ]);
        }

        $holidayData ? Employee::where('employee_id', $employee->employee_id)->update(['weekoff_updated_at' => date('Y-m-d', \strtotime($month))]) : '';

        // Log::info($holidayData);
    }

    public function findWeekdays($month, $day_name)
    {
        $dateList = '';
        $dayKey = '';
        $week_days = [];

        if (isset($month) && isset($day_name)) {
            $week = \weekedName();
            Log::info($week);
            foreach ($week as $dayKey => $weekLi) {
                Log::info($weekLi);
                Log::info($day_name);

                if ($weekLi === $day_name) {
                    $dayKey =  $dayKey;
                    Log::info('DayKey : '.$dayKey);
                    break;
                }
            }

            $dateList = findMonthToAllDate($month);

            foreach ($dateList as $key => $dateLi) {
                if ($dateLi['day_name'] === $dayKey) {
                    Log::info('dateLi : '.$dateLi['date']);
                    $week_days[] .=  $dateLi['date'];
                }
            }
        }
        Log::info($week_days);
        return $week_days;
    }

    public function startRow(): int
    {
        return 2;
    }
}
