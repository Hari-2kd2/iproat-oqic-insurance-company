<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('designation')->truncate();
        DB::table('designation')->insert(
            [
                ['designation_name' => 'Director','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
                ['designation_name' => 'Plant Head','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
                ['designation_name' => 'Admin','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
                ['designation_name' => 'Head of Department','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
                ['designation_name' => 'Staff','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
                ['designation_name' => 'Contract Labour','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],

            ]

        );
    }
}
