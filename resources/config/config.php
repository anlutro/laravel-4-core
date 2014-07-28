<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

return [
	// Whether to enable the package's routes.
	'enable-routes' => true,

	// The URL prefix to put in front of all the package's routes.
	'route-prefix' => null,

	// Whether to queue password reminder emails.
	'queue-reminder-mail' => false,

	// allow "remember me" option when logging in
	'login-remember' => false,

	// Where to redirect after login if no "intended" URL is in session. This
	// will be passed to URL::to(), so a relative URI is good enough.
	'redirect-login' => '/',

	// set this value to enable support email form
	'support-email' => null,
];
