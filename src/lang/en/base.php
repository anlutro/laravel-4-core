<?php
/**
 * This file contains every localization string used by the L4 Base files, as
 * well as some handy re-usable localizations like success, failure messages
 * where you provide your own model string.
 */

return array(

	// semi-localized date/time formats
	'datetime-format' => 'Y-m-d H:i',
	'date-format' => 'Y-m-d',
	'time-format' => 'H:i',

	// generic flash messages
	'generic-success' => 'The request was successful!',
	'create-success' => ':model created!',
	'create-failure' => ':model could not be created.',
	'update-success' => ':model updated!',
	'update-failure' => ':model could not be updated.',
	'delete-success' => ':model deleted.',
	'delete-failure' => ':model could not be deleted.',
	'creating' => 'New :model',
	'editing' => 'Editing :model',
	'not-found' => ':model not found!',

	// generic button strings
	'submit' => 'Submit',
	'save' => 'Save',
	'update' => 'Update',
	'delete' => 'Delete',
	'back' => 'Back',
	'new' => 'Add new',
	'edit' => 'Edit',
	'view' => 'View',
	'search' => 'Search',
	'execute' => 'Execute',
	'with-selected' => 'With selected:',


	// generic auth strings
	'access-denied' => 'You do not have access to this part of the site.',
	'confirm-password' => 'Confirm password',
	'invalid-password' => 'Invalid password.',
	'login-failure' => 'Invalid login, please try again.',
	'login-required' => 'You need to be logged in to access this page.',
	'login-submit' => 'Log in',
	'login-success' => 'You are now logged in!',
	'login-title' => 'Log in',
	'logout' => 'Log out',
	'logout-success' => 'You are now logged out.',
	'reset-success' => 'The password was reset. You may now log in with your new password.',
	'reset-token-invalid' => 'Invalid password token.',
	'resetpass-instructions' => 'If you\'ve forgotten your password, we can send you an e-mail with instructions on how to reset your password.',
	'resetpass-link' => 'Forgot password?',
	'resetpass-send' => 'Send reset instructions',
	'resetpass-sent' => 'Instructions were sent to the e-mail address provided.',
	'resetpass-text' => 'Someone (hopefully you!) has requestd a password reset for your account on :sitename. Go to the link below to reset your password. If you haven\'t requested a password reset, you can safely ignore this message. The link below will expire in one hour.',
	'resetpass-title' => 'Reset password',
	'user-email-notfound' => 'A user with that e-mail was not found.',

	// user controller
	'email-field' => 'E-mail',
	'model-profile' => 'Profile',
	'model-user' => 'User',
	'myuser-title' => 'My user',
	'name-field' => 'Name',
	'password-field' => 'Password',
	'phone-field' => 'Phone',
	'profile-title' => 'My profile',
	'usertype-admin' => 'Administrators',
	'usertype-all' => 'All users',
	'usertype-normal' => 'Normal users',
	'usertype-superuser' => 'Superusers',
	'username-field' => 'Username',
	'usertype-field' => 'Group',
	'new-password' => 'New password',
	'old-password' => 'Old password',
	'updating-password-explanation' => 'You only need to fill in the following forms if you intend to change the password.',

	// admin user controller
	'admin-userlist' => 'List of users',
	'admin-newuser' => 'Add user',

	// other misc stuff
	'browsehappy' => 'You are using an <strong>outdated</strong> browser. Please <a href=":url">upgrade your browser</a> to improve your experience.',
	'page-not-found' => 'The page you were looking for was not found!',
	'under-construction' => 'Under construction',
	'under-construction-text' => 'This part of the website is currently under construction. Please check again later!',
	'from' => 'From',
	'until' => 'Until',
	'token-mismatch' => 'This website uses tokens to protect the server from spam and hacking attempts. The token sent with your request was invalid. Please try again.',

);
