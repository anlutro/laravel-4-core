<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

/**
 * Route config. Note that URLs are overwritten by the corresponding
 * lang/{locale}/routes.php file if it is present.
 */
return [
	/**
	 * Core routes
	 */
	'core' => [
		'login' => [
			'url' => '/user/login',
			'method' => 'get',
			'uses' => 'anlutro\Core\Web\AuthController@login',
			'before' => ['guest'],
		],
		'login_post' => [
			'url' => '/user/login',
			'method' => 'post',
			'uses' => 'anlutro\Core\Web\AuthController@attemptLogin',
			'before' => ['csrf'],
		],
		'profile' => [
			'url' => '/user/profile',
			'method' => 'get',
			'uses' => 'anlutro\Core\Web\UserController@profile',
			'before' => ['auth'],
		],
		'profile_post' => [
			'url' => '/user/profile',
			'method' => 'post',
			'uses' => 'anlutro\Core\Web\UserController@updateProfile',
			'before' => ['auth'],
		],
		'logout' => [
			'url' => '/user/logout',
			'method' => 'get',
			'uses' => 'anlutro\Core\Web\AuthController@logout',
			'before' => ['auth'],
		],
		'user.show' => [
			'url' => '/user/{id}/profile',
			'method' => 'get',
			'uses' => 'anlutro\Core\Web\UserController@show',
			'before' => ['auth'],
		],
		'user.index' => [
			'url' => '/admin/users',
			'method' => 'get',
			'uses' => 'anlutro\Core\Web\UserController@index',
			'before' => ['auth', 'access:admin'],
		],
		'user.bulk' => [
			'url' => '/admin/users',
			'method' => 'post',
			'uses' => 'anlutro\Core\Web\UserController@bulk',
			'before' => ['auth', 'access:admin'],
		],
		'user.create' => [
			'url' => '/admin/users/new',
			'method' => 'get',
			'uses' => 'anlutro\Core\Web\UserController@create',
			'before' => ['auth', 'access:admin'],
		],
		'user.store' => [
			'url' => '/admin/users/new',
			'method' => 'post',
			'uses' => 'anlutro\Core\Web\UserController@store',
			'before' => ['auth', 'access:admin'],
		],
		'user.edit' => [
			'url' => '/admin/users/{id}',
			'method' => 'get',
			'uses' => 'anlutro\Core\Web\UserController@edit',
			'before' => ['auth', 'access:admin'],
		],
		'user.update' => [
			'url' => '/admin/users/{id}',
			'method' => 'post',
			'uses' => 'anlutro\Core\Web\UserController@update',
			'before' => ['auth', 'access:admin'],
		],
		'user.delete' => [
			'url' => '/admin/users/{id}',
			'method' => 'delete',
			'uses' => 'anlutro\Core\Web\UserController@delete',
			'before' => ['auth', 'access:admin'],
		],
	],

	/**
	 * Password reset routes
	 */
	'reminders' => [
		'pwreset.request' => [
			'url' => '/password/request',
			'method' => 'get',
			'uses' => 'anlutro\Core\Web\AuthController@reminder',
			'before' => ['guest'],
		],
		'pwreset.request_post' => [
			'url' => '/password/request',
			'method' => 'post',
			'uses' => 'anlutro\Core\Web\AuthController@sendReminder',
			'before' => ['guest', 'csrf'],
		],
		'pwreset.reset' => [
			'url' => '/password/reset',
			'method' => 'get',
			'uses' => 'anlutro\Core\Web\AuthController@reset',
			'before' => ['guest'],
		],
		'pwreset.reset_post' => [
			'url' => '/password/reset',
			'method' => 'post',
			'uses' => 'anlutro\Core\Web\AuthController@attemptReset',
			'before' => ['guest', 'csrf'],
		],
	],

	/**
	 * Activation routes
	 */
	'activation' => [
		'activation.register' => [
			'url' => '/user/register',
			'method' => 'get',
			'uses' => 'anlutro\Core\Web\AuthController@register',
			'before' => ['guest'],
		],
		'activation.register_post' => [
			'url' => '/user/register',
			'method' => 'post',
			'uses' => 'anlutro\Core\Web\AuthController@attemptRegistration',
			'before' => ['guest', 'csrf'],
		],
		'activation.activate' => [
			'url' => '/user/activate',
			'method' => 'get',
			'uses' => 'anlutro\Core\Web\AuthController@activate',
			'before' => ['guest'],
		],
	],
];
