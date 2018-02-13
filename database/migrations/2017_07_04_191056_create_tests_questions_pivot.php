<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestsQuestionsPivot extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tests_questions_pivot', function(Blueprint $table)
		{
			$table->integer('test_id')->unsigned()->index();
			$table->integer('question_id')->unsigned()->index();
			$table->engine = 'InnoDB';
			$table->primary(array('question_id', 'test_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('tests_questions_pivot');
	}

}
