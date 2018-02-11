<?php

use Log;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Moonlight\Models\User;
use Moonlight\Models\Group;
use Carbon\Carbon;

class MoonlightUserTableSeeder extends Seeder {

	public function run()
	{
		$password = Str::random(8);

		$user = DB::table('admin_users')->insert([
			'login' => 'magus',
			'password' => password_hash($password, PASSWORD_DEFAULT),
			'email' => 'denis-shumeev@yandex.ru',
			'first_name' => 'Super',
			'last_name' => 'Magus',
			'superuser' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
		]);

		Log:info($user->login);
		Log:info($password);
	}
}