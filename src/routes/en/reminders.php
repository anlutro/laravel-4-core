<?php
use Illuminate\Support\Facades\Route;

Route::group(['before' => 'guest'], function()
{
	Route::get('/reminder', 'AuthController@reminder');
	Route::post('/reminder', [
		'before' => 'csrf',
		'uses' => 'AuthController@sendReminder'
	]);
	Route::get('/reset', 'AuthController@reset');
	Route::post('/reset', [
		'before' => 'csrf',
		'uses' => 'AuthController@attemptReset'
	]);
});