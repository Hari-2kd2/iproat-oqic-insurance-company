<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('department')->truncate();
        DB::table('department')->insert(
            [
                ['department_name' => 'Quality Assurance', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Quality Control', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Microbiology ', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Research / Formulation and Development', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Analytical Method Development Laboratory ', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Warehouse', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Production and Packing', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Engineering', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Human Resources and Admin', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Environmental Health and Safety', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Information Technology', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Purchase', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Accounting', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Marketing / Business Development', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],
                ['department_name' => 'Supply Chain Management', 'created_at' => $time, 'updated_at' => $time, 'branch_id' => 1],

            ]

        );
    }
}
