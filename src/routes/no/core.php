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
	Route::get('/logg-inn', 'c\Controllers\AuthController@login');
	Route::post('/logg-inn', [
		'before' => 'csrf',
		'uses' => 'c\Controllers\AuthController@attemptLogin'
	]);	
});

Route::group(['before' => 'auth'], function()
{
	Route::get('/min-profil', 'c\Controllers\UserController@profile');
	Route::post('/min-profil', 'c\Controllers\UserController@updateProfile');
	Route::get('/logg-ut', 'c\Controllers\AuthController@logout');
	Route::get('/profil/{id}', 'c\Controllers\UserController@show');
});

Route::group(['prefix' => 'admin', 'before' => ['auth', 'access:admin']], function()
{
	Route::get('brukere', 'c\Controllers\UserController@index');
	Route::post('brukere', 'c\Controllers\UserController@bulk');
	Route::get('brukere/ny', 'c\Controllers\UserController@create');
	Route::post('bruker/ny', 'c\Controllers\UserController@store');
	Route::get('brukere/{id}', 'c\Controllers\UserController@edit');
	Route::post('brukere/{id}', 'c\Controllers\UserController@update');
	Route::delete('brukere/{id}', 'c\Controllers\UserController@delete');
});
