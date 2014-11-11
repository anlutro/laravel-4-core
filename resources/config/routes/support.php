<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

return [
	'support' => [
		'url' => '/support',
		'method' => 'get',
		'uses' => 'anlutro\Core\Web\SupportController@displayForm',
		'before' => ['auth'],
	],
	'support_post' => [
		'url' => '/support',
		'method' => 'post',
		'uses' => 'anlutro\Core\Web\SupportController@handleForm',
		'before' => ['auth', 'csrf'],
	],
];
