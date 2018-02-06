<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Moonlight\Utils\RussianText;
use Carbon\Carbon;
use App\User;

class LoginController extends Controller
{
	use AuthenticatesUsers;

	protected $redirectTo = '/home';

	public function __construct()
	{
		$this->middleware('guest')->except('logout');
	}

	public function login(Request $request)
	{
		$scope = [];

		$email = $request->input('email');
		$password = $request->input('password');
		$remember = $request->input('remember');

		$scope['email'] = $email;
		$scope['password'] = $password;
		$scope['remember'] = $remember;

		if (! $email) {
			$scope['error'] = 'Введите e-mail.';
			return view('auth.login', $scope);
		}

		if (! $password) {
			$scope['error'] = 'Введите пароль.';
			return view('auth.login', $scope);
		}

		if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $seconds = $this->limiter()->availableIn(
				$this->throttleKey($request)
			);
	
			$scope['error'] = 
				'Слишком много попыток. '
				.'Попробуйте войти через '.$seconds.' '
				.RussianText::selectCaseForNumber($seconds, ['секунду', 'секунды', 'секунд'])
				.'.';
	
			return view('auth.login', $scope);
		}
		
		$this->incrementLoginAttempts($request);

		$user = User::where('email', $email)->first();

		if (! $user) {
			$scope['error'] = 'Неправильный e-mail или пароль.';
			return view('auth.login', $scope);
		}

		if (! Hash::check($password, $user->password)) {
			$scope['error'] = 'Неправильный логин или пароль.';
			return view('auth.login', $scope);
		}

		if (! $user->activated) {
			$scope['error'] = 'Пользователь не активирован.';
			return view('auth.login', $scope);
		}

		if ($user->banned) {
			$scope['error'] = 'Пользователь заблокирован.';
			return view('auth.login', $scope);
		}

		Auth::login($user, $remember);

		$request->session()->regenerate();

		return redirect()->route('home');
	}

	public function logout(Request $request)
	{
		$scope = [];

		Auth::logout();

		$request->session()->invalidate();

		return redirect()->back();
	}

	public function index(Request $request)
	{
		$scope = [];

		$scope['email'] = null;
		$scope['password'] = null;
		$scope['remember'] = false;
		$scope['error'] = null;

		return view('auth.login', $scope);
	}

} 