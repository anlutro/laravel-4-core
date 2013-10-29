<?php
use Illuminate\Support\Facades\Route;

Route::group(['before' => 'guest'], function()
{
	Route::get('/login', 'AuthController@login');
	Route::post('/login', [
		'before' => 'csrf',
		'uses' => 'AuthController@attemptLogin'
	]);
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

Route::group(['before' => 'auth'], function()
{
	Route::get('/my-profile', 'UserController@viewProfile');
	Route::post('/my-profile', 'UserController@updateProfile');
	Route::get('/logout', 'UserController@logout');
});

Route::group(['prefix' => 'admin', 'before' => ['auth', 'access:admin']], function()
{
	Route::get('users', 'UserController@userList');
	Route::post('users', 'UserController@bulkUserAction');
	Route::get('users/new', 'UserController@newUser');
	Route::post('users/new', 'UserController@createNewUser');
	Route::get('users/{id}', 'UserController@showUser');
	Route::post('users/{id}', 'UserController@updateUser');
	Route::delete('users/{id}', 'UserController@deleteUser');
});
