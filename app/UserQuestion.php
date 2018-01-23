<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserQuestion extends Model
{
    use SoftDeletes;

    public static function boot()
	{
		parent::boot();

        static::created(function($element) {
            cache()->tags('user_questions')->flush();
        });

        static::saved(function($element) {
            cache()->tags('user_questions')->flush();
        });

        static::deleted(function($element) {
            cache()->tags('user_questions')->flush();
        });
    }

	public function answers()
    {
        return $this->hasMany('App\UserAnswer');
    }
}