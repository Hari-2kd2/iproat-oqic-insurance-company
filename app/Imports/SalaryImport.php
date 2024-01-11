<?php

namespace App\Imports;

use App\Model\SalaryDetails;
use Maatwebsite\Excel\Collections\ToModel;

class SalaryImport implements ToModel
{

    public function model(array $rows)
    {
            return new SalaryDetails([
                'employee_id'           => $row[19],
                'month_of_salary'       => $lastMonth,
                'basic_salary'          => $row[6],
                // 'total_allowance'       => $row[0],
                 'total_deduction'       => $row[15],
                // 'total_late'            => $row[0],
                 // 'total_late_amount'     => $row[0],
                 // 'total_absence'         => $row[0],
                 'total_absence_amount'  => $row[8],
                // 'overtime_rate'         => $row[0],
                 // 'per_day_salary'        => $row[0],
                 // 'total_over_time_hour'  => $row[0],
                 'total_overtime_amount' => $row[17],
                // 'hourly_rate'           => $row[0],
                 // 'total_present'         => $row[0],
                 // 'total_leave'           => $row[0],
                 // 'total_working_days'    => $row[0],
                 'net_salary'            => $row[6],
                'tax'                   => $row[14],
                'taxable_salary'        => $row[6],
                // 'working_hour'        => $row[0],
                 'gross_salary'          => $row[18],
                'created_by'            => Auth::user()->user_id,
                'updated_by'            => Auth::user()->user_id,
                'status'                => 1,
                'comment'               => null,
                'payment_method'        => 'cash',
                'action'                => 'monthlySalary',
                'created_at'            => Carbon::now(),
                'updated_at'            => Carbon::now(),
            ]);
        }

}
