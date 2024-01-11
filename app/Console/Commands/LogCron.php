<?php

namespace App\Console\Commands;

use App\Http\Controllers\View\EmployeeAttendaceController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LogCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sqllog:import';
    protected $name      = "sqllog-import";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run this command to import raw attendance logs for forign database';

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
        Log::info("Log cron is working fine!");
        $controller = new EmployeeAttendaceController();
        $controller->fetchRawLog();

        /*
    Write your database logic we bellow:
    Item::create(['name'=>'hello new']);
     */
    }
}
