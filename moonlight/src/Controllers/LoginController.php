<?php

namespace Moonlight\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Moonlight\Main\UserActionType;
use Moonlight\Models\User;
use Moonlight\Models\UserAction;

class LoginController extends Controller
{
    /**
     * Login.
     * 
     * @return Response
     */
    
    public function login(Request $request)
    {
        $scope = [];

		$login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->input('remember');

        $scope['login'] = $login;
        $scope['remember'] = $remember;

		if (! $login) {
			$scope['error'] = 'Введите логин.';
			return view('moonlight::login', $scope);
		}

		if (! $password) {
			$scope['error'] = 'Введите пароль.';
			return view('moonlight::login', $scope);
		}

		$user = User::where('login', $login)->first();

		if (! $user) {
			$scope['error'] = 'Неправильный логин или пароль.';
			return view('moonlight::login', $scope);
		}
        
		if (! password_verify($password, $user->password)) {
			$scope['error'] = 'Неправильный логин или пароль.';
			return view('moonlight::login', $scope);
		}

		if ($user->banned) {
			$scope['error'] = 'Пользователь заблокирован.';
			return view('moonlight::login', $scope);
        }
        
        Auth::guard('moonlight')->login($user, $remember);
        
        UserAction::log(
			UserActionType::ACTION_TYPE_LOGIN_ID,
			'ID '.$user->id.' ('.$user->login.')'
		);

        return redirect()->route('moonlight.home')
            ->withCookie(cookie()->forever('login', $user->login))
            ->withCookie(cookie()->forever('remember', $remember));
    }
    
    /**
     * Logout.
     * 
     * @return Response
     */
    
    public function logout(Request $request)
    {
        $loggedUser = Auth::guard('moonlight')->user();
        
        UserAction::log(
			UserActionType::ACTION_TYPE_LOGOUT_ID,
			'ID '.$loggedUser->id.' ('.$loggedUser->login.')'
		);

        Auth::guard('moonlight')->logout();
        
        return redirect()->route('moonlight.login');
    }
    
    /**
     * Login form.
     * 
     * @return View
     */
    
    public function show(Request $request)
    {
        $scope['login'] = $request->cookie('login');
        $scope['remember'] = $request->cookie('remember');
        
        return view('moonlight::login', $scope);
    }
}