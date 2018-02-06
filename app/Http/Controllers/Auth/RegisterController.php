<?php

namespace App\Http\Controllers\Auth;

use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\User;
use App\Mail\Register;
use App\Mail\AdminRegister;

class RegisterController extends Controller
{
	public function __construct()
	{
		$this->middleware('guest');
	}

	public function complete(Request $request)
    {
		$scope = [];

		return view('auth.register.complete', $scope);
    }

    public function activate(Request $request)
    {
		$scope = [];

		$email = $request->input('email');
		$token = $request->input('token');

		$user = User::where('email', $email)->first();

		if (! $user) {
			return view('auth.register.failed', $scope);
		}

		if ($token !== $user->remember_token) {
			return view('auth.register.failed', $scope);
		}

		$user->activated = true;
		$user->remember_token = null;

		$user->save();

		Auth::login($user);

		return redirect()->route('home', ['register' => 'complete']);
    }

	public function success(Request $request)
    {
		$scope = [];

		return view('auth.register.success', $scope);
    }

	public function register(Request $request)
	{
		$email = $request->input('email');
		$password = $request->input('password');
		$first_name = $request->input('first_name');
		$last_name = $request->input('last_name');
		$human = $request->input('human');

		$validator = Validator::make($request->all(), [
			'email' => [
				'required',
				'email',
				Rule::unique('users', 'email')->where(function ($query) {
					return $query->where('deleted_at', null);
				}),
			],
			'password' => 'required',
            'first_name' => 'required',
			'last_name' => 'required',
			'human' => 'required',
        ], [
			'email.required' => 'Введите e-mail.',
            'email.email' => 'Некорректный e-mail.',
			'email.unique' => 'Такой e-mail уже зарегистрирован.',
			'password.required' => 'Придумайте пароль.',
            'first_name.required' => 'Введите имя.',
			'last_name.required' => 'Введите фамилию.',
			'human.required' => 'Подтвердите, что вы не робот.',
        ]);
        
        if ($validator->fails()) {
			$scope['errors'] = $validator->errors();
			$scope['email'] = $email;
			$scope['password'] = $password;
			$scope['first_name'] = $first_name;
			$scope['last_name'] = $last_name;
			$scope['human'] = $human;
            
            return view('auth.register.index', $scope);
        }

		$user = new User;

		$user->email = $email;
		$user->password = Hash::make($password);
		$user->first_name = $first_name;
		$user->last_name = $last_name;
		$user->service_section_id = 1;
		$user->activated = false;
		$user->remember_token = Str::random(60);

		$user->save();

		Mail::send(new Register($user));

		Mail::send(new AdminRegister($user));

		return redirect()->route('register.success');
	}

	public function index(Request $request)
	{
		$scope = [];

		$scope['email'] = null;
		$scope['password'] = null;
		$scope['first_name'] = null;
		$scope['last_name'] = null;
		$scope['human'] = false;

		return view('auth.register.index', $scope);
	}
} 