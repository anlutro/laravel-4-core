<?php
use Illuminate\Support\Facades\Route;

Route::group(['before' => 'guest'], function()
{
	Route::get('/logg-inn', 'AuthController@login');
	Route::post('/logg-inn', [
		'before' => 'csrf',
		'uses' => 'AuthController@attemptLogin'
	]);
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

Route::group(['before' => 'auth'], function()
{
	Route::get('/min-profil', 'UserController@viewProfile');
	Route::post('/min-profil', 'UserController@updateProfile');
	Route::get('/logg-ut', 'UserController@logout');
});

Route::group(['prefix' => 'admin', 'before' => ['auth', 'access:admin']], function()
{
	Route::get('brukere', 'UserController@userList');
	Route::post('brukere', 'UserController@bulkUserAction');
	Route::get('brukere/ny', 'UserController@newUser');
	Route::post('bruker/ny', 'UserController@createNewUser');
	Route::get('brukere/{id}', 'UserController@showUser');
	Route::post('brukere/{id}', 'UserController@updateUser');
	Route::delete('brukere/{id}', 'UserController@deleteUser');
});
