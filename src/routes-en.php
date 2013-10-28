<?php

Route::group(['before' => 'guest'], function()
{
	Route::get('/login', 'UserController@login');
	Route::post('/login', [
		'before' => 'csrf',
		'uses' => 'UserController@attemptLogin'
	]);
	Route::get('/reminder', 'UserController@reminder');
	Route::post('/reminder', [
		'before' => 'csrf',
		'uses' => 'UserController@sendReminder'
	]);
	Route::get('/reset', 'UserController@reset');
	Route::post('/reset', [
		'before' => 'csrf',
		'uses' => 'UserController@attemptReset'
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
	Route::get('/users', 'AdminUserController@userList');
	Route::post('/users', 'AdminUserController@bulkUserAction');
	Route::get('/users/new', 'AdminUserController@newUser');
	Route::post('/users/new', 'AdminUserController@createNewUser');
	Route::get('/users/{id}', 'AdminUserController@showUser');
	Route::post('/users/{id}', 'AdminUserController@updateUser');
	Route::post('/users/{id}/slett', 'AdminUserController@deleteUser');
});