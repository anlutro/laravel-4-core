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
	Route::get('/registrer', 'AuthController@register');
	Route::post('/registrer', 'AuthController@attemptRegistration');
	Route::get('/aktiver', 'AuthController@activate');
});
