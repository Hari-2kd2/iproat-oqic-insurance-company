<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeAttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('employee_attendance')->truncate();
        for ($i = 01; $i <= 02; $i++) {
            for ($j = 01; $j <= 02; $j++) {
                DB::table('employee_attendance')->insert(
                    [
                        ['finger_print_id' => '1001', 'in_out_time' => '2022-' . $i . '-' . $j . ' ' . '21:00:00', 'created_at' => $time, 'updated_at' => $time],
                        ['finger_print_id' => '1001', 'in_out_time' => '2022-' . $i . '-' . $j . ' ' . '22:30:00', 'created_at' => $time, 'updated_at' => $time],
                        ['finger_print_id' => '1001', 'in_out_time' => '2022-' . $i . '-' . $j . ' ' . '23:30:00', 'created_at' => $time, 'updated_at' => $time],
                        ['finger_print_id' => '1001', 'in_out_time' => '2022-' . $i . '-' . $j . ' ' . '01:30:00', 'created_at' => $time, 'updated_at' => $time],
                        ['finger_print_id' => '1001', 'in_out_time' => '2022-' . $i . '-' . $j . ' ' . '02:30:00', 'created_at' => $time, 'updated_at' => $time],
                        ['finger_print_id' => '1001', 'in_out_time' => '2022-' . $i . '-' . $j . ' ' . '05:30:00', 'created_at' => $time, 'updated_at' => $time],
                        ['finger_print_id' => '1001', 'in_out_time' => '2022-' . $i . '-' . $j . ' ' . '07:30:00', 'created_at' => $time, 'updated_at' => $time],
                    ]
                );
            }
        }
    }
}
