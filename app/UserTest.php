<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTest extends Model
{
	use SoftDeletes;
	
	public function getDates()
	{
		return [
			'complete_at',
			'created_at',
			'updated_at',
			'deleted_at',
		];
    }

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
	
	public function user()
    {
        return $this->belongsTo('App\User');
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
