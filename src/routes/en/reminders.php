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
	Route::get('/reminder', 'c\Controllers\AuthController@reminder');
	Route::post('/reminder', [
		'before' => 'csrf',
		'uses' => 'c\Controllers\AuthController@sendReminder'
	]);
	Route::get('/reset', 'c\Controllers\AuthController@reset');
	Route::post('/reset', [
		'before' => 'csrf',
		'uses' => 'c\Controllers\AuthController@attemptReset'
	]);
});