<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SyncToLive extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();
        DB::table('sync_to_live')->truncate();
        DB::table('sync_to_live')->insert(
            [
                ['status' => '0','created_at'=>$time,'updated_at'=>$time, 'branch_id' => 1],
            ]

        );
    }
}
