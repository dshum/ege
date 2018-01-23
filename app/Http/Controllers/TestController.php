<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Test;
use App\Question;
use App\Answer;
use App\UserTest;
use App\UserQuestion;
use App\UserAnswer;
use Log;

class TestController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function save(Request $request, $id)
	{
		$scope = [];

		$user = Auth::user();

        $test = cache()->tags('tests')->remember("test_{$id}", 1440, function() use ($id) {
			return Test::where('id', $id)->first();
		});

        if (! $test) {
            return redirect()->route('welcome');
        }

		$userTest = cache()->tags('user_tests')->remember("user_test_where_user_{$user->id}_and_test_{$test->id}", 1440, function() use ($user, $test) {
			return 
				UserTest::where([
					['user_id', $user->id],
					['test_id', $test->id],
				])->first();
		});

		if (! $userTest) {
			$userTest = new UserTest;

			$userTest->name = $test->name;
			$userTest->user_id = $user->id;
			$userTest->test_id = $test->id;

			$userTest->save();
		}

		$answers = $request->input('answers');

		if (empty($answers)) {
			return redirect()->back();
		}

		foreach ($answers as $questionId => $answerId) {
			$question = Question::where('id', $questionId)->first();
			$answer = Answer::where('id', $answerId)->first();

			if (! $question) continue;
			if (! $answer) continue;

			$userQuestion = UserQuestion::where('question_id', $question->id)->first();

			if (! $userQuestion) {
				$userQuestion = new UserQuestion;

				$userQuestion->user_test_id = $userTest->id;
				$userQuestion->question_id = $question->id;
			}

			if ($answer->correct) {
				$userQuestion->correct = true;
			}

			$userQuestion->name = $question->name;

			$userQuestion->save();

			$userAnswer = UserAnswer::where('user_question_id', $userQuestion->id)->first();

			if (! $userAnswer) {
				$userAnswer = new UserAnswer;

				$userAnswer->user_question_id = $userQuestion->id;
			} elseif ($userAnswer->answer_id !== $answer->id) {
				$userAnswer->forceDelete();

				$userAnswer = new UserAnswer;

				$userAnswer->user_question_id = $userQuestion->id;
			}

			$userAnswer->name = $answer->name;
			$userAnswer->answer_id = $answer->id;

			$userAnswer->save();
		}

		$questionsCount = $test->questions()->count();
		$userQuestionsCount = UserQuestion::where('user_test_id', $userTest->id)->count();

		if ($questionsCount === $userQuestionsCount) {
			$userTest->complete = true;

			$userTest->save();
		}

		return redirect()->route('home');
	}

	public function index(Request $request, $id)
	{
		$scope = [];

		$user = Auth::user();
		
		$test = cache()->tags('tests')->remember("test_{$id}", 1440, function() use ($id) {
			return Test::where('id', $id)->first();
		});

        if (! $test) {
            return redirect()->route('welcome');
        }

		$questionAnswered = [];
		$answerChecked = [];

		$userTest = cache()->tags('user_tests')->remember("user_test_where_user_{$user->id}_and_test_{$test->id}", 1440, function() use ($user, $test) {
			return 
				UserTest::where([
					['user_id', $user->id],
					['test_id', $test->id],
				])->first();
		});

		if ($userTest) {
			$userQuestions = cache()->tags('user_questions')->remember("user_test_{$userTest->id}_questions", 1440, function() use ($userTest) {
				return $userTest->questions()->get();
			});

			foreach ($userQuestions as $userQuestion) {
				$userAnswers = cache()->tags('user_answers')->remember("user_question_{$userQuestion->id}_answers", 1440, function() use ($userQuestion) {
					return $userQuestion->answers()->get();
				});

				foreach ($userAnswers as $userAnswer) {
					$answerChecked[$userAnswer->answer_id] = true;
				}

				$questionAnswered[$userQuestion->question_id] = $userQuestion;
			}
		}

		$questions = cache()->tags('questions')->remember("test_{$test->id}_questions", 1440, function() use ($test) {
			return $test->questions()->orderBy('order')->get();
		});

		foreach ($questions as $question) {
			$answers[$question->id] = cache()->tags('answers')->remember("question_{$question->id}_answers", 1440, function() use ($question) {
				return $question->answers()->orderBy('order')->get();
			});
		}

		$scope['userTest'] = $userTest;
		$scope['questionAnswered'] = $questionAnswered;
		$scope['answerChecked'] = $answerChecked;
        $scope['test'] = $test;
		$scope['questions'] = $questions;
		$scope['answers'] = $answers;

		return view('test', $scope);
	}

} 