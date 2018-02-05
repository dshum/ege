<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{
	use SoftDeletes;
	
	public static function boot()
	{
		parent::boot();

		static::created(function($element) {
			cache()->tags('topics')->flush();
		});

		static::saved(function($element) {
            cache()->tags('topics')->flush();
		});

		static::deleted(function($element) {
            cache()->tags('topics')->flush();
		});
    }

	public function subject()
	{
		return $this->belongsTo('App\Subject', 'subject_id');
	}
}
