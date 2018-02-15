<?php

Route::post('/questions/filter', ['uses' => 'QuestionFilter@filter']);

Route::post('/answers/{id}', ['uses' => 'Answers@correct']);

Route::post('/loader', ['uses' => 'TestLoader@load']);

Route::get('/photos', ['uses' => 'PhotoLoader@getPhotos']);
Route::post('/photos/load', ['uses' => 'PhotoLoader@loadPhoto']);
Route::post('/photos/delete', ['uses' => 'PhotoLoader@deletePhoto']);