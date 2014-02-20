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
	Route::get('/login', 'c\Controllers\AuthController@login');
	Route::post('/login', [
		'before' => 'csrf',
		'uses' => 'c\Controllers\AuthController@attemptLogin'
	]);
});

Route::group(['before' => 'auth'], function()
{
	Route::get('/my-profile', 'c\Controllers\UserController@profile');
	Route::post('/my-profile', 'c\Controllers\UserController@updateProfile');
	Route::get('/logout', 'c\Controllers\AuthController@logout');
	Route::get('/profile/{id}', 'c\Controllers\UserController@show');
});

Route::group(['prefix' => 'admin', 'before' => 'auth|access:admin'], function()
{
	Route::get('users', 'c\Controllers\UserController@index');
	Route::post('users', 'c\Controllers\UserController@bulk');
	Route::get('users/new', 'c\Controllers\UserController@create');
	Route::post('users/new', 'c\Controllers\UserController@store');
	Route::get('users/{id}', 'c\Controllers\UserController@edit');
	Route::post('users/{id}', 'c\Controllers\UserController@update');
	Route::delete('users/{id}', 'c\Controllers\UserController@delete');
});
