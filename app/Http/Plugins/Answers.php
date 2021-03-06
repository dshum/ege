<?php 

namespace App\Http\Plugins;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Moonlight\Main\Item;
use Moonlight\Main\Element;
use App\Http\Controllers\Controller;
use App\Question;
use App\Answer;

class Answers extends Controller {

    public function correct(Request $request, $id)
	{
		$scope = [];

        $currentAnswer = Answer::find($id);

        if (! $currentAnswer) {
            $scope['error'] = 'Ответ не найден.';

            return response()->json($scope);
        }

        $question = $currentAnswer->question;

        $answers = $question->answers()->get();

        if ($question->isSingle()) {
            foreach ($answers as $answer) {
                $answer->correct = $answer->id === $currentAnswer->id
                    ? true : false;
                $answer->save();
            }
        } elseif ($question->isMultiple()) {
            foreach ($answers as $answer) {
                if ($answer->id === $currentAnswer->id) {
                    $answer->correct = ! $answer->correct;
                    $answer->save();
                }
            }
        }

        $scope['answers'] = [];

        foreach ($answers as $answer) {
            $scope['answers'][$answer->id] = $answer->correct;
        }

		return response()->json($scope);
	}

	public function field($question)
	{
		$scope = [];

		$answers = $question->answers()->orderBy('order')->get();

        $scope['answers'] = $answers;

		return view('plugins.answers.field', $scope);
    }

} 