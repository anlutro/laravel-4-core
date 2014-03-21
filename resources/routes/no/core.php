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
	Route::get('/logg-inn', 'anlutro\Core\Web\AuthController@login');
	Route::post('/logg-inn', [
		'before' => 'csrf',
		'uses' => 'anlutro\Core\Web\AuthController@attemptLogin'
	]);	
});

Route::group(['before' => 'auth'], function()
{
	Route::get('/min-profil', 'anlutro\Core\Web\UserController@profile');
	Route::post('/min-profil', 'anlutro\Core\Web\UserController@updateProfile');
	Route::get('/logg-ut', 'anlutro\Core\Web\AuthController@logout');
	Route::get('/profil/{id}', 'anlutro\Core\Web\UserController@show');
});

Route::group(['prefix' => 'admin', 'before' => ['auth', 'access:admin']], function()
{
	Route::get('brukere', 'anlutro\Core\Web\UserController@index');
	Route::post('brukere', 'anlutro\Core\Web\UserController@bulk');
	Route::get('brukere/ny', 'anlutro\Core\Web\UserController@create');
	Route::post('bruker/ny', 'anlutro\Core\Web\UserController@store');
	Route::get('brukere/{id}', 'anlutro\Core\Web\UserController@edit');
	Route::post('brukere/{id}', 'anlutro\Core\Web\UserController@update');
	Route::delete('brukere/{id}', 'anlutro\Core\Web\UserController@delete');
});
