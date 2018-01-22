<?php 

namespace App\Http\Plugins;

use Illuminate\Http\Request;
use Cache;
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

        $users = Cache::tags('User')->remember('lastUsers', 1440, function() {
            return User::orderBy('created_at', 'desc')->limit(10)->get();
        });

        $scope['lastUsers'] = [];

        foreach ($users as $user) {
            $lastUser = [
                'id' => $user->id,
                'classId' => Element::getClassId($user),
                'email' => $user->email,
                'name' => $user->first_name.' '.$user->last_name,
                'tests' => [],
            ];

            $userTests = Cache::tags('UserTest')->remember("userTests[{$user->id}]", 1440, function() use ($user) {
                return UserTest::where('user_id', $user->id)->
                    orderBy('created_at', 'desc')->
                    get();
            });

            foreach ($userTests as $userTest) {
                $test = Cache::tags('Test')->remember("userTest[{$userTest->id}][test]", 1440, function() use ($userTest) {
                    return $userTest->test()->first();
                });

                $questionCount = Cache::tags('Question')->remember("test[{$test->id}][questions][count]", 1440, function() use ($test) {
                    return $test->questions()->count();
                });

                $userQuestionCount = Cache::tags('UserQuestion')->remember("userTest[{$userTest->id}][questions][count]", 1440, function() use ($userTest) {
                    return $userTest->questions()->count();
                });

                $userCorrectQuestionCount = Cache::tags('UserQuestion')->remember("userTest[{$userTest->id}][questions][correct][count]", 1440, function() use ($userTest) {
                    return $userTest->questions()->where('correct', 1)->count();
                });

                $lastUser['tests'][] = [
                    'id' => $userTest->id,
                    'classId' => Element::getClassId($userTest),
                    'name' => $userTest->name,
                    'created_at' => $userTest->created_at->format('d.m.Y, H:i'),
                    'complete' => $userTest->complete,
                    'total' => $questionCount,
                    'answered' => $userQuestionCount,
                    'incorrect' => $userQuestionCount - $userCorrectQuestionCount,
                    'correct' => $userCorrectQuestionCount,
                    'percent' => round(100 * $userQuestionCount / $questionCount),
                ];
            }

            $scope['users'][] = $lastUser;
        }

		return view('plugins.welcome', $scope);
	}

} 