<?php

namespace App\Listeners;

use App\Events\AccessLogEvent;
use App\Http\Controllers\Attendance\GenerateReportController;
use App\Jobs\ReportJob;
use App\Model\Employee;
use App\Model\MsSql;
use Illuminate\Support\Facades\DB;

class AccessLogListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    public $generateReportController;

    public function __construct(GenerateReportController $generateReportController)
    {
        $this->generateReportController = $generateReportController;
    }

    /**
     * Handle the event.
     *
     * @param  AccessLogEvent  $event
     * @return void
     */
    public function handle(AccessLogEvent $event)
    {
        try {
            DB::beginTransaction();
            $input = $event->data;
            $data = MsSql::create($input);
            $datetime = $data->datetime;

            if ($data->type == 'OUT') {
                $last_in = MsSql::where('ID', $data->ID)->where('type', 'IN')->orderByDesc('datetime')->first();
                if (isset($last_in)) {
                    $datetime = $last_in->datetime;
                }
            }

            $emp = Employee::where('finger_id', $data->ID)->select('employee_id')->first();
            dispatch(new ReportJob($data->ID, $emp->employee_id, date('Y-m-d', strtotime($datetime))));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            //throw $th;
        }
    }
}
