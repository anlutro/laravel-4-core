<?php
use Illuminate\Support\Facades\Route;

Route::group(['before' => 'guest'], function()
{
	Route::get('/register', 'AuthController@register');
	Route::post('/register', 'AuthController@attemptRegistration');
	Route::get('/activate', 'AuthController@activate');
});
