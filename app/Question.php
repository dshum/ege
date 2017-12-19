<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    /**
	 * The Eloquent test model.
	 *
	 * @var string
	 */
	public static $testModel = 'App\Test';

	/**
	 * The tests-questions pivot table name.
	 *
	 * @var string
	 */
	public static $testsQuestionsPivot = 'tests_questions_pivot';
    
    public function tests()
	{
		return $this->belongsToMany(static::$testModel, static::$testsQuestionsPivot);
	}

	public function answers()
    {
        return $this->hasMany('App\Answer');
    }

    public function getAnswersInfo()
    {
        $plugin = new \App\Http\Plugins\Answers;

        return $plugin->field($this);
    }
}
