<?php

namespace App\Jobs;

use App\Http\Controllers\Attendance\GenerateReportController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $finger_id;
    public $employee_id;
    public $date;
    public $in_time;
    public $out_time;
    public $manualAttendance;
    public $recompute;

    public function __construct($finger_id, $employee_id, $date, $in_time = "", $out_time = "", $manualAttendance = false, $recompute = true)
    {
        $this->finger_id = $finger_id;
        $this->employee_id = $employee_id;
        $this->date = $date;
        $this->in_time = $in_time;
        $this->out_time = $out_time;
        $this->manualAttendance = $manualAttendance;
        $this->recompute = $recompute;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fn = new GenerateReportController();
        $fn->calculate_attendance($this->finger_id, $this->employee_id, $this->date, $this->in_time, $this->out_time, $this->manualAttendance, $this->recompute);
    }
}
