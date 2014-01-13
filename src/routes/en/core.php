<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

use Illuminate\Support\Facades\Route;

Route::group(['before' => 'guest'], function()
{
	Route::get('/login', 'AuthController@login');
	Route::post('/login', [
		'before' => 'csrf',
		'uses' => 'AuthController@attemptLogin'
	]);
});

Route::group(['before' => 'auth'], function()
{
	Route::get('/my-profile', 'UserController@profile');
	Route::post('/my-profile', 'UserController@updateProfile');
	Route::get('/logout', 'AuthController@logout');
	Route::get('/profile/{id}', 'UserController@show');
});

Route::group(['prefix' => 'admin', 'before' => 'auth|access:admin'], function()
{
	Route::get('users', 'UserController@index');
	Route::post('users', 'UserController@bulk');
	Route::get('users/new', 'UserController@create');
	Route::post('users/new', 'UserController@store');
	Route::get('users/{id}', 'UserController@edit');
	Route::post('users/{id}', 'UserController@update');
	Route::delete('users/{id}', 'UserController@delete');
});
