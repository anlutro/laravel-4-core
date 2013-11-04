<?php
use Illuminate\Support\Facades\Route;

Route::group(['before' => 'guest'], function()
{
	Route::get('/glemt-passord', 'AuthController@reminder');
	Route::post('/glemt-passord', [
		'before' => 'csrf',
		'uses' => 'AuthController@sendReminder'
	]);
	Route::get('/tilbakestill-passord', 'AuthController@reset');
	Route::post('/tilbakestill-passord', [
		'before' => 'csrf',
		'uses' => 'AuthController@attemptReset'
	]);
});
