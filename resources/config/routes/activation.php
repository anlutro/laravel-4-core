<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

return [
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
];
