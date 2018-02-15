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
    protected $signature = 'background:load';

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
        $url = 'http://yandex.ru/images/today?size=1920x1080';
        $path = public_path().'/assets/background.jpg';

        $date = file_exists($path) ? date('Y-m-d', filemtime($path)) : null;

        if ($date < date('Y-m-d')) {
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
