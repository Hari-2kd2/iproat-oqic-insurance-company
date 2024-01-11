<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AllClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'all:clear';

    protected $description = 'all clear command';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $driver = config('session.driver');
        
        $method_name = 'clean' . ucfirst($driver);
        if (method_exists($this, $method_name)) {
            try {
                $this->$method_name();
                $this->info('Application session cache cleared!');
                $this->optimizeClear();

            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        } else {
            $this->error("Sorry, I don't know how to clean the sessions of the driver '{$driver}'.");
        }
        return true;

    }

    protected function cleanFile()
    {
        $directory = config('session.files');
        $ignoreFiles = ['.gitignore', '.', '..'];

        $files = scandir($directory);

        foreach ($files as $file) {
            if (!in_array($file, $ignoreFiles)) {
                unlink($directory . '/' . $file);
            }
        }
    }
    protected function optimizeClear()
    {
        Artisan::call('config:clear');
        $this->info('Configuration cache cleared!');

        Artisan::call('cache:clear');
        $this->info('Application cache cleared!');

        Artisan::call('view:clear');
        $this->info('Compiled views cleared!');

        Artisan::call('route:clear');
        $this->info('Route cache cleared!');

        Artisan::call('clear-compiled');
        $this->info('Compiled services and packages files removed!');

        Artisan::call('debugbar:clear');
        $this->info('Application debugbar cache cleared...');

        Artisan::call('auth:clear-resets');
        $this->info('Application auth:clear-resets!');
    }

}
