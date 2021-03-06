<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;

    public static function boot()
	{
		parent::boot();

		static::created(function($element) {
			cache()->tags('subjects')->flush();
		});

		static::saved(function($element) {
            cache()->tags('subjects')->flush();
		});

		static::deleted(function($element) {
            cache()->tags('subjects')->flush();
		});
    }
}
