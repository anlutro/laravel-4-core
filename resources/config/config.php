<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

return array(
	/**
	 * Whether to enable the package's routes.
	 */
	'enable-routes' => true,

	/**
	 * The URL prefix to put in front of all the package's routes.
	 */
	'route-prefix' => null,

	/**
	 * Whether to queue password reminder emails.
	 */
	'queue-reminder-mail' => false,

	/**
	 * Where to redirect after login if no "intended" URL is in session.
	 */
	'redirect-login' => '/',
);
