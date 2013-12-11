<?php
use Illuminate\Support\Facades\Route;

Route::group(['before' => 'guest'], function()
{
	Route::get('/registrer', 'AuthController@register');
	Route::post('/registrer', 'AuthController@attemptRegistration');
	Route::get('/aktiver', 'AuthController@activate');
});
