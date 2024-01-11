<?php

namespace App\Console\Commands;

use App\Components\Common;
use App\Model\LastRecordID;
use Illuminate\Console\Command;
use App\Model\EmployeeInOutData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExportAttendance extends Command
{

    protected $signature   = 'attendance:export';
    protected $description = 'Export Attendance';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::beginTransaction();

        $client   = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->request('GET', Common::liveurl() . "reporthistory");
        $json = $response->getBody()->getContents();

        $json = json_decode($json);
        if (isset($json->data->employee_attendance_id))
            $serverID = $json->data->employee_attendance_id;
        else
            $serverID = 0;

        $local_empID = EmployeeInOutData::orderBy('employee_attendance_id', 'DESC')->first();

        if ($serverID  && $local_empID->employee_attendance_id == $serverID) {
            return true;
        }


        EmployeeInOutData::where('employee_attendance_id', '>', $serverID)->orderBy('employee_attendance_id', 'ASC')->chunk(5, function ($attendanceReport) {
            Log::info($attendanceReport);

            foreach ($attendanceReport as $Data) {

                // $headers = ['Content-Type' => 'application/json'];
                // $headers = ['Accept' => 'application/json'];
                $client   = new \GuzzleHttp\Client(['verify' => false]);
                $response = $client->request('POST', Common::liveurl() . "importattendance", [
                    'form_params' => [
                        'employee_attendance_id' => $Data->employee_attendance_id,
                        'finger_print_id'        => $Data->finger_print_id,
                        'date'                   => $Data->date,
                        'in_time_from'           => $Data->in_time_from,
                        'in_time'                => $Data->in_time,
                        'out_time'               => $Data->out_time,
                        'out_time_upto'          => $Data->out_time_upto,
                        'working_time'           => $Data->working_time,
                        'working_hour'           => $Data->working_hour,
                        'status'                 => $Data->status,
                        'created_at'             => $Data->created_at,
                        'updated_at'             => $Data->updated_at,
                        'over_time'              => $Data->over_time,
                        'early_by'               => $Data->early_by,
                        'late_by'                => $Data->late_by,
                        'shift_name'             => $Data->shift_name,
                        'in_out_time'             => $Data->in_out_time,
                    ],
                ]);
                $Data->live_status = 1;
                $Data->save();
            }
        });

        DB::commit();
    }
}
