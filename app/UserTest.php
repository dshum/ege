<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTest extends Model
{
    use SoftDeletes;

    public static function boot()
	{
		parent::boot();

		static::created(function($element) {
			cache()->tags('user_tests')->flush();
		});

		static::saved(function($element) {
            cache()->tags('user_tests')->flush();
		});

		static::deleted(function($element) {
            cache()->tags('user_tests')->flush();
		});
    }

    public function test()
    {
        return $this->belongsTo('App\Test');
    }

    public function questions()
	{
		return $this->hasMany('App\UserQuestion');
	}
}
