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

class UserStatistics extends Controller {

	public function index($user)
	{
        $scope = [];
        
        $statistics = [];

        $userTests = cache()->tags('user_tests')->remember("user_{$user->id}_tests", 1440, function() use ($user) {
            return UserTest::where('user_id', $user->id)->
                orderBy('created_at', 'desc')->
                get();
        });

        foreach ($userTests as $userTest) {
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

            $statistics[] = [
                'id' => $userTest->id,
                'classId' => Element::getClassId($userTest),
                'name' => $userTest->name,
                'created_at' => $userTest->created_at->format('d.m.Y, H:i'),
                'complete' => $userTest->complete,
                'total' => $total,
                'answered' => $answered,
                'correct' => $correct,
                'incorrect' => $answered - $correct,
                'percent' => round(100 * $correct / $total),
            ];
        }

        $scope['statistics'] = $statistics;

		return view('plugins.userStatistics', $scope);
	}

} 