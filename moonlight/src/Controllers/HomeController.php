<?php

namespace Moonlight\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Moonlight\Main\Element;
use Moonlight\Models\FavoriteRubric;
use Moonlight\Models\Favorite;

class HomeController extends Controller
{   
    /**
     * Show home view.
     *
     * @return View
     */
    public function index(Request $request)
    {
        $scope = [];
        
        $loggedUser = Auth::guard('moonlight')->user();

        $site = \App::make('site');

        /*
         * Home styles and scripts
         */

        $styles = $site->getHomeStyles();
        $scripts = $site->getHomeScripts();

        /*
         * Home plugin
         */
        
        $homePluginView = null;
        
        $homePlugin = $site->getHomePlugin();

        if ($homePlugin) {
            $view = \App::make($homePlugin)->index();

            if ($view) {
                $homePluginView = is_string($view)
                    ? $view : $view->render();
            }
        }
        
        $rubricController = new RubricController;
        
        $rubrics = $rubricController->index();

        $scope['homePluginView'] = $homePluginView;
        $scope['rubrics'] = $rubrics;

        view()->share([
            'styles' => $styles,
            'scripts' => $scripts,
        ]);
            
        return view('moonlight::home', $scope);
    }
}