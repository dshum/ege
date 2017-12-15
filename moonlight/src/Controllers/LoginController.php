<?php

namespace Moonlight\Controllers;

use Illuminate\Http\Request;
use Moonlight\Main\LoggedUser;
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

		if ( ! $login) {
			$scope['message'] = 'Введите логин.';
			return view('moonlight::login', $scope);
		}
        
        $scope['login'] = $login;

		if ( ! $password) {
			$scope['message'] = 'Введите пароль.';
			return view('moonlight::login', $scope);
		}

		$user = User::where('login', $login)->first();

		if ( ! $user) {
			$scope['message'] = 'Неправильный логин или пароль.';
			return view('moonlight::login', $scope);
		}
        
		if ( ! password_verify($password, $user->password)) {
			$scope['message'] = 'Неправильный логин или пароль.';
			return view('moonlight::login', $scope);
		}

		if ($user->banned) {
			$scope['message'] = 'Пользователь заблокирован.';
			return view('moonlight::login', $scope);
		}

        LoggedUser::login($user);
        
        UserAction::log(
			UserActionType::ACTION_TYPE_LOGIN_ID,
			'ID '.$user->id.' ('.$user->login.')'
		);

        return redirect()->route('moonlight.home')->withCookie(cookie()->forever('login', $user->login));
    }
    
    /**
     * Logout.
     * 
     * @return Response
     */
    
    public function logout(Request $request)
    {
        $loggedUser = LoggedUser::getUser();
        
        UserAction::log(
			UserActionType::ACTION_TYPE_LOGOUT_ID,
			'ID '.$loggedUser->id.' ('.$loggedUser->login.')'
		);
        
        LoggedUser::logout();
        
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
        
        return view('moonlight::login', $scope);
    }
}