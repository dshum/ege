<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAnswer extends Model
{
    use SoftDeletes;

    public static function boot()
	{
		parent::boot();

        static::created(function($element) {
            cache()->tags('user_answers')->flush();
        });

        static::saved(function($element) {
            cache()->tags('user_answers')->flush();
        });

        static::deleted(function($element) {
            cache()->tags('user_answers')->flush();
        });
    }
}