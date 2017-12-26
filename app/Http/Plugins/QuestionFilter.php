<?php 

namespace App\Http\Plugins;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Validator;
use Carbon\Carbon;
use Moonlight\Main\Item;
use Moonlight\Main\Element;
use App\Question;
use App\Answer;

class QuestionFilter {

    public function filter(Request $request)
    {
        $scope = [];

        $text = $request->input('text');

        $request->session()->put('plugins[text]', $text);
        
        return response()->json($scope);
    }

    public function handle($criteria)
	{
        $text = Session::has('plugins[text]') ? Session::get('plugins[text]') : null;

        if ($text) {
            $criteria->where('question', 'ilike', "%$text%");
        }

		return $criteria;
    }
    
    public function index(Item $item)
	{
        $scope = [];

        $text = Session::has('plugins[text]') ? Session::get('plugins[text]') : null;
        
        $scope['item'] = $item;
        $scope['text'] = $text;

		return view('plugins.questionFilter', $scope);
	}

} 