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
	Route::get('/logg-inn', 'AuthController@login');
	Route::post('/logg-inn', [
		'before' => 'csrf',
		'uses' => 'AuthController@attemptLogin'
	]);	
});

Route::group(['before' => 'auth'], function()
{
	Route::get('/min-profil', 'UserController@profile');
	Route::post('/min-profil', 'UserController@updateProfile');
	Route::get('/logg-ut', 'AuthController@logout');
	Route::get('/profil/{id}', 'UserController@show');
});

Route::group(['prefix' => 'admin', 'before' => ['auth', 'access:admin']], function()
{
	Route::get('brukere', 'UserController@index');
	Route::post('brukere', 'UserController@bulk');
	Route::get('brukere/ny', 'UserController@create');
	Route::post('bruker/ny', 'UserController@store');
	Route::get('brukere/{id}', 'UserController@edit');
	Route::post('brukere/{id}', 'UserController@update');
	Route::delete('brukere/{id}', 'UserController@delete');
});
