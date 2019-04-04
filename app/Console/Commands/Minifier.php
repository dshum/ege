<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MatthiasMullie\Minify;

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
                $src = $resources.'/source/css/'.$file.'.css';
                $dest = $resources.'/assets/css/'.$file.'.min.css';

                $minifier = new Minify\CSS($src);

                $minifier->minify($dest);

                echo $dest.PHP_EOL;
            } catch (\ErrorException $e) {
                echo $dest.' FAILED'.PHP_EOL;
            }
        }

        foreach ($js as $file) {
            try {
                $src = $resources.'/source/js/'.$file.'.js';
                $dest = $resources.'/assets/js/'.$file.'.min.js';

                $minifier = new Minify\JS($src);

                $minifier->minify($dest);

                echo $dest.PHP_EOL;
            } catch (\ErrorException $e) {
                echo $dest.' FAILED'.PHP_EOL;
            }
        }
    }
}
