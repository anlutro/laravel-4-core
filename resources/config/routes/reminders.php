<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

return [
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
];