<?php

namespace App\Console\Commands;

use App\Http\Controllers\Attendance\GenerateReportController;
use Illuminate\Console\Command;

class ReportCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report';
    protected $name = "report";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run this to create attendacne report';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        info('Please wait report generation in progress');
        print_r('Please wait');
        echo "\n";

        // return true;
        // $leaveRepository = new LeaveRepository;
        // $attendanceRepository = new AttendanceRepository;
        // Log::info("Attendance cron is working fine!");
        // $controller = new EmployeeAttendaceController($leaveRepository, $attendanceRepository);
        // $controller->attendance();

        $controller = new GenerateReportController();
        $controller->generateAttendanceReport(date('Y-m-d'));

    }
}
