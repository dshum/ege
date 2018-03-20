<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Minifier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minify {type?} {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Minify moonlight css and js files.';

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
        $type = $this->argument('type');
        $file = $this->argument('file');

        $resources = base_path().'/moonlight/src/resources';
        $cssUrl = 'https://cssminifier.com/raw';
        $jsUrl = 'https://javascript-minifier.com/raw';

        if ($type == 'css' && $file) {
            $css = [$file];
            $js = [];
        } elseif ($type == 'js' && $file) {
            $css = [];
            $js = [$file];
        } else {
            $css = [
                'autocomplete',
                'browse',
                'calendar',
                'default',
                'edit',
                'favorites',
                'groups',
                'home',
                'loader',
                'log',
                'login',
                'permissions',
                'search',
                'trash',
                'trashed',
                'users',
            ];

            $js = [
                'browse',
                'common',
                'edit',
                'favorites',
                'group',
                'groups',
                'home',
                'log',
                'login',
                'password',
                'permissions',
                'profile',
                'reset',
                'search',
                'trash',
                'trashed',
                'user',
                'users',
            ];
        }

        foreach ($css as $file) {
            try {
                $input = File::get($resources.'/source/css/'.$file.'.css');

                $params = ['input' => $input];

                $result = file_get_contents($cssUrl, false, stream_context_create([
                    'http' => [
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => http_build_query($params)
                    ]
                ]));

                File::put($resources.'/assets/css/'.$file.'.min.css', $result);

                echo $file.'.css'.PHP_EOL;
            } catch (\ErrorException $e) {
                echo $file.'.css FAILED'.PHP_EOL;
            }

            sleep(1);
        }

        foreach ($js as $file) {
            try {
                $input = File::get($resources.'/source/js/'.$file.'.js');

                $params = ['input' => $input];

                $result = file_get_contents($jsUrl, false, stream_context_create([
                    'http' => [
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => http_build_query($params)
                    ]
                ]));

                File::put($resources.'/assets/js/'.$file.'.min.js', $result);

                echo $file.'.js'.PHP_EOL;
            } catch (\ErrorException $e) {
                echo $file.'.js FAILED'.PHP_EOL;
            }

            sleep(1);
        }
    }
}
