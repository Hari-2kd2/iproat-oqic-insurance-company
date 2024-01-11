<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-dHmi') . ".sql";
        $path = "C:/xampp/mysql/bin/mysqldump";
        $username = 'root'; //env('DB_USERNAME')
        $password = ''; //env('DB_PASSWORD')
        $host = '127.0.0.1'; //env('DB_HOST');
        $database = 'hrms'; //env('DB_DATABASE')
        $command = $path . " --user=" . $username . " --password=" . $password . " --host=" . $host . " " . $database . "  > " . storage_path() . "/app/backup/" . $filename;

        $returnVar = NULL;
        $output  = NULL;

        exec($command, $output, $returnVar);
    }
}
