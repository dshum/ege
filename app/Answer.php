<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use SoftDeletes;

    public static function boot()
	{
		parent::boot();

		static::created(function($element) {
			cache()->tags('answers')->flush();
		});

		static::saved(function($element) {
            cache()->tags('answers')->flush();
		});

		static::deleted(function($element) {
            cache()->tags('answers')->flush();
		});
    }

    public function question()
    {
        return $this->belongsTo('App\Question');
    }
}
