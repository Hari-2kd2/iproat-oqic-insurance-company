<?php

namespace App\Console\Commands;

use App\Http\Controllers\View\EmployeeAttendaceController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NewEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:cron';
    protected $name      = "employee-cron";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run to create newly added employee report';

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
        Log::info("New Employee cron is working fine!");
        $controller = new EmployeeAttendaceController();
        $controller->samsungNewEmployees();

        /*
    Write your database logic we bellow:
    Item::create(['name'=>'hello new']);
     */
    }
}
