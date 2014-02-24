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
	Route::get('/glemt-passord', 'c\Controllers\AuthController@reminder');
	Route::post('/glemt-passord', [
		'before' => 'csrf',
		'uses' => 'c\Controllers\AuthController@sendReminder'
	]);
	Route::get('/tilbakestill-passord', 'c\Controllers\AuthController@reset');
	Route::post('/tilbakestill-passord', [
		'before' => 'csrf',
		'uses' => 'c\Controllers\AuthController@attemptReset'
	]);
});
