<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class MsSqlLog extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function attData1($empIDs, $in, $out, $t_in, $b_out, $b_in, $t_out, $t_stamp, $pid)
    {

        foreach ($empIDs as $key => $id) {
            // //entry
            DB::connection('sqlsrv')->table('atteninfo')->insert([
                'ID'       => $id,
                'deviceno' => $in,
                'datetime' => $t_in,
                'date'     => date('Y-m-d', strtotime($t_in)),
                'time'     => date('H:i:s', strtotime($t_in)),
                'status'   => "I",
                'device'   => "testing",

            ]);

        }
    }
    public function attData2($empIDs, $in, $out, $t_in, $b_out, $b_in, $t_out, $t_stamp, $pid)
    {

        foreach ($empIDs as $key => $id) {

            DB::connection('sqlsrv')->table('atteninfo')->insert([
                'ID'       => $id,
                'deviceno' => $out,
                'datetime' => $b_out,
                'date'     => date('Y-m-d', strtotime($b_out)),
                'time'     => date('H:i:s', strtotime($b_out)),
                'status'   => "O",
                'device'   => "testing",

            ]);

        }
    }
    public function attData3($empIDs, $in, $out, $t_in, $b_out, $b_in, $t_out, $t_stamp, $pid)
    {

        foreach ($empIDs as $key => $id) {

            DB::connection('sqlsrv')->table('atteninfo')->insert([
                'ID'       => $id,
                'deviceno' => $in,
                'datetime' => $b_in,
                'date'     => date('Y-m-d', strtotime($b_in)),
                'time'     => date('H:i:s', strtotime($b_in)),
                'status'   => "I",
                'device'   => "testing",

            ]);

        }
    }

    public function attData4($empIDs, $in, $out, $t_in, $b_out, $b_in, $t_out, $t_stamp, $pid)
    {

        foreach ($empIDs as $key => $id) {

            // //   exit
            DB::connection('sqlsrv')->table('atteninfo')->insert([
                'ID'       => $id,
                'deviceno' => $out,
                'datetime' => $t_out,
                'date'     => date('Y-m-d', strtotime($t_out)),
                'time'     => date('H:i:s', strtotime($t_out)),
                'status'   => "O",
                'device'   => "testing",

            ]);
        }
    }

    public function run()
    {
        $time_stamp  = Carbon::now();
        $in_devices  = array(8257, 9457);
        $out_devices = array(3059, 9333);
        $in          = Arr::random($in_devices);
        $out         = Arr::random($out_devices);
        $pid         = 1;
        $i           = 1;
        $j           = 2;

        // DB::connection('sqlsrv')->table('atteninfo')->truncate();

        //first
        $mor_time_in        = Carbon::createFromFormat('Y-m-d H:i:s', '2022-01-' . $j . ' ' . '06:00:00');
        $mor_time_break_out = Carbon::createFromFormat('Y-m-d H:i:s', '2022-01-' . $j . ' ' . '08:45:00');
        $mpr_time_break_in  = Carbon::createFromFormat('Y-m-d H:i:s', '2022-01-' . $j . ' ' . '09:30:00');
        $mor_time_out       = Carbon::createFromFormat('Y-m-d H:i:s', '2022-01-' . $j . ' ' . '23:50:00');

        for ($k = 41; $k <= 50; $k++) {
            $empList_night_shift = array('P0' . sprintf('%02d', $k));

            // $this->attData1($empList_night_shift, $in, $out, $mor_time_in, $mor_time_break_out, $mpr_time_break_in, $mor_time_out, $time_stamp, $pid);
            // $this->attData2($empList_night_shift, $in, $out, $mor_time_in, $mor_time_break_out, $mpr_time_break_in, $mor_time_out, $time_stamp, $pid);
            // $this->attData3($empList_night_shift, $in, $out, $mor_time_in, $mor_time_break_out, $mpr_time_break_in, $mor_time_out, $time_stamp, $pid);
            // $this->attData4($empList_night_shift, $in, $out, $mor_time_in, $mor_time_break_out, $mpr_time_break_in, $mor_time_out, $time_stamp, $pid);
        }
    }

}
