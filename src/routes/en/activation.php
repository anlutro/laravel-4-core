<?php
use Illuminate\Support\Facades\Route;

Route::group(['before' => 'guest'], function()
{
	Route::get('/activate', 'AuthController@activate');
	Route::post('/activate', [
		'before' => 'csrf',
		'uses' => 'AuthController@attemptActivation'
	]);
});
