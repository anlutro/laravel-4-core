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

	// Allow "remember me" option when logging in.
	'login-remember' => false,

	// Where to redirect after login if no "intended" URL is in session. This
	// will be passed to URL::to(), so a relative URI is good enough.
	'redirect-login' => '/',

	// An optional message to display as a session flash message on login.
	'login-message' => null,

	// Where to send support message emails. If null/false/empty string, the
	// support message routes will be disabled.
	'support-email' => null,

	// Whether to enable the link to the home URL (as defined in this config
	// file's `redirect-login`) in the top menu. Can be a closure that is
	// lazily invoked.
	'enable-home-link' => function() { return Illuminate\Support\Facades\Auth::check(); },
];
