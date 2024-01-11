<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaidLeaveRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('paid_leave_rules')->truncate();
        DB::table('paid_leave_rules')->insert(
            [
                ['for_year' => '1', 'branch_id' => 1, 'day_of_paid_leave' => '20', 'created_at' => $time, 'updated_at' => $time],
                ['for_year' => '1', 'branch_id' => 2, 'day_of_paid_leave' => '20', 'created_at' => $time, 'updated_at' => $time],
            ]

        );
    }
}
