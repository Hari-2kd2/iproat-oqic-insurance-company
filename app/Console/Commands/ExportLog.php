<?php

namespace App\Console\Commands;

use App\Components\Common;
use App\Model\LastRecordID;
use Illuminate\Console\Command;
use App\Model\DeviceAttendanceLog;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Employee\AccessController;
use App\Model\MsSql;

class ExportLog extends Command
{

    protected $signature = 'devicelog:export';
    protected $name = 'devicelog-export';
    protected $description = 'Export Device Log';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        DB::beginTransaction();

        $client   = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->request('GET', Common::liveurl() . "loghistory");
        $json = $response->getBody()->getContents();

        $json = json_decode($json);
        if (isset($json->data->primary_id))
            $serverID = $json->data->primary_id;
        else
            $serverID = 0;

        $local_logID = MsSql::orderBy('primary_id', 'DESC')->first();

        if ($serverID  && $local_logID->primary_id == $serverID) {
            return true;
        }

        DeviceAttendanceLog::where('primary_id', '>', $serverID)->orderBy('primary_id', 'ASC')->chunk(5, function ($device_log) {

            foreach ($device_log as $logs) {

                $client   = new \GuzzleHttp\Client(['verify' => false]);
                $response = $client->request('POST', Common::liveurl() . "importlogs", [
                    'form_params' => [
                        'primary_id' => $logs->primary_id,
                        'evtlguid' => $logs->evtlguid,
                        'ID' => $logs->ID,
                        'type' => $logs->type,
                        'datetime' => $logs->datetime,
                        'status' => $logs->status,
                        'created_at' => $logs->created_at,
                        'updated_at' => $logs->updated_at,
                        'employee' => $logs->employee,
                        'device' => $logs->device,
                        'device_employee_id' => $logs->device_employee_id,
                        'sms_log' => $logs->sms_log,
                    ]
                ]);

                $logs->live_status = 1;
                $logs->save();
            }
        });

        DB::commit();
    }
}
