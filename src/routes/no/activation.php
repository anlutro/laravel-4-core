<?php
use Illuminate\Support\Facades\Route;

Route::group(['before' => 'guest'], function()
{
	Route::get('/aktiver', 'AuthController@activate');
	Route::post('/aktiver', [
		'before' => 'csrf',
		'uses' => 'AuthController@attemptActivation'
	]);
});
