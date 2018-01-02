<?php

Route::post('/questions/filter', ['uses' => 'QuestionFilter@filter']);

Route::post('/answers/{id}', ['uses' => 'Answers@correct']);

Route::post('/loader', ['uses' => 'TestLoader@load']);