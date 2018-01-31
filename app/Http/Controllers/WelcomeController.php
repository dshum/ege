<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subject;
use App\Topic;
use App\Subtopic;
use App\Test;

class WelcomeController extends Controller
{
	public function __construct()
	{
		// $this->middleware('guest');
	}

	public function index(Request $request)
	{
		$scope = [];

		if ($request->has('make_test_error')) {
			1/0;
		}

		$subjects = cache()->tags('subjects')->remember('subjects', 1440, function() {
			return Subject::where('hidden', false)->orderBy('order')->get();
		});

		$topics = cache()->tags('topics')->remember('topics', 1440, function() {
			return Topic::where('hidden', false)->orderBy('order')->get();
		});

		$subtopics = cache()->tags('subtopics')->remember('subtopics', 1440, function() {
			return Subtopic::where('hidden', false)->orderBy('order')->get();
		});

		$tests = cache()->tags('tests')->remember('tests', 1440, function() {
			return Test::orderBy('order')->get();
		});

		$scope['subjects'] = $subjects;
		$scope['topics'] = $topics;
		$scope['subtopics'] = $subtopics;
		$scope['tests'] = $tests;

		return view('welcome', $scope);
	}
} 