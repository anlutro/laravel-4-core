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
	Route::get('/login', 'anlutro\Core\Web\AuthController@login');
	Route::post('/login', [
		'before' => 'csrf',
		'uses' => 'anlutro\Core\Web\AuthController@attemptLogin'
	]);
});

Route::group(['before' => 'auth'], function()
{
	Route::get('/my-profile', 'anlutro\Core\Web\UserController@profile');
	Route::post('/my-profile', 'anlutro\Core\Web\UserController@updateProfile');
	Route::get('/logout', 'anlutro\Core\Web\AuthController@logout');
	Route::get('/profile/{id}', 'anlutro\Core\Web\UserController@show');
});

Route::group(['prefix' => 'admin', 'before' => 'auth|access:admin'], function()
{
	Route::get('users', 'anlutro\Core\Web\UserController@index');
	Route::post('users', 'anlutro\Core\Web\UserController@bulk');
	Route::get('users/new', 'anlutro\Core\Web\UserController@create');
	Route::post('users/new', 'anlutro\Core\Web\UserController@store');
	Route::get('users/{id}', 'anlutro\Core\Web\UserController@edit');
	Route::post('users/{id}', 'anlutro\Core\Web\UserController@update');
	Route::delete('users/{id}', 'anlutro\Core\Web\UserController@delete');
});
