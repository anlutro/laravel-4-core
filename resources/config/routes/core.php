<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

return [
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
		'before' => ['auth', 'csrf'],
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
		'before' => ['auth', 'access:admin', 'csrf'],
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
		'before' => ['auth', 'access:admin', 'csrf'],
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
		'before' => ['auth', 'access:admin', 'csrf'],
	],
	'user.delete' => [
		'url' => '/admin/users/{id}',
		'method' => 'delete',
		'uses' => 'anlutro\Core\Web\UserController@delete',
		'before' => ['auth', 'access:admin', 'csrf'],
	],
	'user.restore' => [
		'url' => '/admin/users/{id}/restore',
		'method' => 'post',
		'uses' => 'anlutro\Core\Web\UserController@restore',
		'before' => ['auth', 'access:admin', 'csrf'],
	],
	'user.switch' => [
		'url' => '/admin/users/{id}/switch',
		'method' => 'get',
		'uses' => 'anlutro\Core\Web\UserController@switchUser',
		'before' => ['auth', 'access:superadmin'],
	],
];
