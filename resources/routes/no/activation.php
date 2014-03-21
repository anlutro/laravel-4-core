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
	Route::get('/registrer', 'anlutro\Core\Web\AuthController@register');
	Route::post('/registrer', 'anlutro\Core\Web\AuthController@attemptRegistration');
	Route::get('/aktiver', 'anlutro\Core\Web\AuthController@activate');
});
