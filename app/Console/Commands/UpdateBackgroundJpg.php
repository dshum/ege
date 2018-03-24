<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Moonlight\Utils\ErrorMessage;

class UpdateBackgroundJpg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'background:load {--force}';

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
        $force = $this->option('force');

        // $url = 'http://yandex.ru/images/today?size=1920x1080'; // doesn't work anymore

        $url = 'https://source.unsplash.com/1920x1080/?nature';
        $path = public_path().'/assets/background.jpg';

        $this->info($path);

        $date = file_exists($path) ? date('Y-m-d', filemtime($path)) : null;

        if ($force || $date < date('Y-m-d')) {
            $file = file($url);

            if ($f = fopen($path, 'w')) {
                foreach ($file as $line) {
                    fwrite($f, $line);
                }

                fclose($f);

                $this->info('Background.jpg loaded.');
            }

            $this->info('OK. Complete.');
        } else {
            $this->info('Background.jpg is up-to-date.');
        }
    }
}