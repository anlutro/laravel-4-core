<?php
/**
 * Laravel 4 Core - Auth Controller
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

/**
 * Controller for authentication actions.
 */
class AuthController extends \c\Controller
{
	/**
	 * Show the login form.
	 *
	 * @return View
	 */
	public function login()
	{
		return View::make('c::auth.login', [
			'formAction' => $this->urlAction('attemptLogin'),
			'resetUrl' => $this->urlAction('reminder'),
		]);
	}

	/**
	 * Attempt to log a user in.
	 *
	 * @return Redirect
	 */
	public function attemptLogin()
	{
		$credentials = [
			'username' => Input::get('username'),
			'password' => Input::get('password'),
		];

		if (Auth::attempt($credentials)) {
			return Redirect::intended('/')
				->with('success', Lang::get('c::auth.login-success'));
		} else {
			return $this->redirectAction('login')
				->withErrors(Lang::get('c::auth.login-failure'));
		}
	}

	/**
	 * Log out the currently logged in user.
	 *
	 * @return Redirect
	 */
	public function logout()
	{
		Auth::logout();
		return $this->redirectAction('login')
			->with('info', Lang::get('c::auth.logout-success'));
	}

	/**
	 * Generate the form for sending a password reset token.
	 *
	 * @return View
	 */
	public function reminder()
	{
		return View::make('c::auth.reminder', [
			'formAction' => $this->urlAction('sendReminder'),
		]);
	}

	/**
	 * Send a password reset token.
	 *
	 * @return Redirect
	 */
	public function sendReminder()
	{
		$credentials = Input::only('email');
		$user = Password::findUser($credentials);

		if (!$user) {
			return $this->redirectAction('reminder')
				->withErrors(Lang::get('c::auth.user-email-notfound'));
		}

		if (Password::requestReset($user)) {
			return $this->redirectAction('login')
				->with('info', Lang::get('c::auth.reminder-sent'));
		} else {
			return $this->redirectAction('reminder')
				->withErrors(Lang::get('c::std.failure'));
		}
	}

	/**
	 * Show the form for finalizing a password reset.
	 *
	 * @return View
	 */
	public function reset()
	{
		if (!Request::query('token'))
			return $this->redirectAction('login');

		return View::make('c::auth.reset', [
			'formAction' => $this->urlAction('attemptReset'),
			'token' => Request::query('token'),
		]);
	}

	/**
	 * Attempt to reset a user's password.
	 *
	 * @return Redirect
	 */
	public function attemptReset()
	{
		$credentials = Input::only('username');
		$token = Input::get('token');
		$input = Input::only('password', 'password_confirmation');

		$validator = Validator::make($input, [
			'password' => ['required', 'confirmed', 'min:5'],
		]);
		
		if ($validator->fails()) {
			return $this->redirectAction('reset')
				->withErrors($validator);
		}

		if (!$user = Password::findUser($credentials)) {
			return $this->redirectAction('login')
				->withErrors(Lang::get('reminders.user'));
		}

		$newPassword = Input::get('password');

		if (Password::resetUser($user, $token, $newPassword)) {
			return $this->redirectAction('login')
				->with('success', Lang::get('c::auth.reset-success'));
		} else {
			return $this->redirectAction('login')
				->withErrors(Lang::get('reminders.token'));
		}
	}
}
