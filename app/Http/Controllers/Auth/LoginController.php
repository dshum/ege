<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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

		$validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Введите e-mail.',
            'email.string' => 'E-mail должен быть строкой.',
            'password.required' => 'Введите пароль.',
            'password.string' => 'Пароль должен быть строкой.',
        ]);

		$email = $request->input('email');
		$password = $request->input('password');
		$remember = $request->input('remember');

		$scope['email'] = $email;
		$scope['password'] = $password;
		$scope['remember'] = $remember;

		if ($validator->fails()) {
            $scope['errors'] = $validator->errors();
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

		if (! Auth::attempt([
			'email' => $email, 
			'password' => $password, 
			'activated' => true, 
			'banned' => false
		], $remember)) {
			$scope['error'] = 'Неправильный e-mail или пароль.';
			return view('auth.login', $scope);
		}

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