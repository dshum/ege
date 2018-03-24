<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subtopic extends Model
{
	use SoftDeletes;
	
	public static function boot()
	{
		parent::boot();

		static::created(function($element) {
			cache()->tags('subtopics')->flush();
		});

		static::saved(function($element) {
            	cache()->tags('subtopics')->flush();
		});

		static::deleted(function($element) {
            	cache()->tags('subtopics')->flush();
		});
    	}

    	public function topic()
	{
		return $this->belongsTo('App\Topic', 'topic_id');
	}
}
