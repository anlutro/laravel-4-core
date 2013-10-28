<?php
use Illuminate\Support\Facades\Route;

Route::group(['before' => 'guest'], function()
{
	Route::get('/logg-inn', 'UserController@login');
	Route::post('/logg-inn', [
		'before' => 'csrf',
		'uses' => 'UserController@attemptLogin'
	]);
	Route::get('/glemt-passord', 'UserController@reminder');
	Route::post('/glemt-passord', [
		'before' => 'csrf',
		'uses' => 'UserController@sendReminder'
	]);
	Route::get('/tilbakestill-passord', 'UserController@reset');
	Route::post('/tilbakestill-passord', [
		'before' => 'csrf',
		'uses' => 'UserController@attemptReset'
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
	Route::get('brukere', 'AdminUserController@userList');
	Route::post('brukere', 'AdminUserController@bulkUserAction');
	Route::get('brukere/ny', 'AdminUserController@newUser');
	Route::post('bruker/ny', 'AdminUserController@createNewUser');
	Route::get('brukere/{id}', 'AdminUserController@showUser');
	Route::post('brukere/{id}', 'AdminUserController@updateUser');
	Route::post('brukere/{id}/slett', 'AdminUserController@deleteUser');
});
