<?php 

namespace App\Http\Plugins;

use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Moonlight\Main\Element;
use App\Http\Controllers\Controller;
use App\User;
use App\Topic;
use App\Test;
use App\Question;
use App\Answer;
use App\UserTest;
use App\UserQuestion;

class Welcome extends Controller {

	public function index()
	{
		$scope = [];

        $recent = [];

        $userTests = cache()->tags('user_tests')->remember("recent_tests", 1440, function() {
            return UserTest::where('complete', true)->
                orderBy('complete_at', 'desc')->
                orderBy('created_at', 'desc')->
                get();
        });

        foreach ($userTests as $userTest) {
            $user = cache()->tags('users')->remember("user_test_{$userTest->id}_user", 1440, function() use ($userTest) {
                return $userTest->user()->first();
            });

            $test = cache()->tags('tests')->remember("user_test_{$userTest->id}_test", 1440, function() use ($userTest) {
                return $userTest->test()->first();
            });

            $total = cache()->tags('questions')->remember("test_{$test->id}_questions_count", 1440, function() use ($test) {
                return $test->questions()->count();
            });

            $answered = cache()->tags('user_questions')->remember("user_test_{$userTest->id}_questions_count", 1440, function() use ($userTest) {
                return $userTest->questions()->count();
            });

            $correct = cache()->tags('user_questions')->remember("user_test_{$userTest->id}_correct_questions_count", 1440, function() use ($userTest) {
                return $userTest->questions()->where('correct', 1)->count();
            });

            $completeAt = $userTest->complete_at
                ? $userTest->complete_at->format('d.m.Y, H:i')
                : null;

            $recent[] = [
                'id' => $userTest->id,
                'classId' => class_id($userTest),
                'name' => $userTest->name,
                'created_at' => $userTest->created_at->format('d.m.Y, H:i'),
                'complete' => $userTest->complete,
                'complete_at' => $completeAt,
                'total' => $total,
                'answered' => $answered,
                'correct' => $correct,
                'incorrect' => $answered - $correct,
                'percent' => round(100 * $correct / $total),
                'user' => [
                    'id' => $user->id,
                    'classId' => class_id($user),
                    'email' => $user->email,
                    'name' => $user->first_name.' '.$user->last_name,
                ],
            ];
        }

        $scope['recent'] = $recent;

		return view('plugins.welcome', $scope);
	}

} 