<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump database.';

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
        $output = [];

        $filename = storage_path().'/backups/biology_'.date('Y-m-d').'.txt';

        $output[] = system("pg_dump --username=forge --format plain --verbose --file {$filename} --dbname=biology");

        foreach ($output as $line) {
            $this->info($line);
        }

        $this->info('OK. Complete.');
    }
}
