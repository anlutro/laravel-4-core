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
	Route::get('/reminder', 'anlutro\Core\Web\AuthController@reminder');
	Route::post('/reminder', [
		'before' => 'csrf',
		'uses' => 'anlutro\Core\Web\AuthController@sendReminder'
	]);
	Route::get('/reset', 'anlutro\Core\Web\AuthController@reset');
	Route::post('/reset', [
		'before' => 'csrf',
		'uses' => 'anlutro\Core\Web\AuthController@attemptReset'
	]);
});