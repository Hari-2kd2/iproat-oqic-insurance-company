<?php

namespace App\Console\Commands;

use App\Components\CamAttendance;
use Illuminate\Console\Command;

class CamAttendanceLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cam-attendance:log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cam-attendance log description';

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
        return CamAttendance::multipleDayLog();
    }
}
