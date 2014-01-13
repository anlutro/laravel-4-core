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