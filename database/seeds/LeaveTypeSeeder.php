<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('leave_type')->truncate();
        DB::table('leave_type')->insert(
            [
                ['leave_type_name' => 'Earn Leave', 'num_of_day' => '0', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['leave_type_name' => 'Paid Leave', 'num_of_day' => '0', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['leave_type_name' => 'Casual Leave', 'num_of_day' => '22', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['leave_type_name' => 'Sick Leave	', 'num_of_day' => '20', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
            ]
        );
    }
}
