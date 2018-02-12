<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateBackgroundJpg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'background';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load a photo from Yandex Images.';

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
        $this->info('Complete.');
    }
}
