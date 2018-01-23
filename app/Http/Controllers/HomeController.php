<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Test;
use App\UserTest;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function save(Request $request)
	{
        $scope = [];
        
        $user = Auth::user();
        
		$first_name = $request->input('first_name');
		$last_name = $request->input('last_name');
        $password = $request->input('password');
        
		$validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
			'password' => 'confirmed',
        ], [
            'first_name.required' => 'Введите имя',
            'last_name.required' => 'Введите фамилию',
			'password.confirmed' => 'Введенные пароли должны совпадать',
        ]);
        
        if ($validator->fails()) {
			$scope['user'] = $user;
			$scope['errors'] = $validator->errors();
			$scope['first_name'] = $first_name;
			$scope['last_name'] = $last_name;
            
            return view('profile', $scope);
        }

		$user->first_name = $first_name;
        $user->last_name = $last_name;
        
		if ($password) {
			$user->password = password_hash($password, PASSWORD_DEFAULT);
        }
        
        $user->save();
        
		return redirect()->route('profile');
	}

    public function profile(Request $request)
	{
        $scope = [];
        
        $user = Auth::user();
        
		$scope['user'] = $user;
		$scope['first_name'] = $user->first_name;
        $scope['last_name'] = $user->last_name;

		return view('profile', $scope);
	}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $scope = [];

        $user = Auth::user();
        
        $userTests = cache()->tags('user_tests')->remember("user_{$user->id}_tests", 1440, function() use ($user) {
            return UserTest::where('user_id', $user->id)->
                orderBy('created_at', 'desc')->
                get();
        });
        
        $tests = [];

		foreach ($userTests as $userTest) {
			$test = cache()->tags('tests')->remember("user_test_{$userTest->id}_test", 1440, function() use ($userTest) {
                return $userTest->test()->first();
            });
            
			$tests[] = $test;
        }
        
        $scope['userTests'] = $userTests;
        
		return view('home', $scope);
    }
}