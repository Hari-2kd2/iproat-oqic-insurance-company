<?php

use Carbon\Carbon;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('role')->truncate();
        DB::table('role')->insert(
            [
                ['role_name' => 'Super Admin','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
                ['role_name' => 'Admin','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
                ['role_name' => 'HR','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
                ['role_name' => 'Head of Department','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
                ['role_name' => 'General Manager','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
                ['role_name' => 'Assistant Manager','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
                ['role_name' => 'Engineer','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
                ['role_name' => 'Staff','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
                ['role_name' => 'Contract Labour','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
            ]

        );
    }
}
