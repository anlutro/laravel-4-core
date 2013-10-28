<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AuthController extends anlutro\L4Base\Controller
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
		// @todo replace this somehow
		$user = $this->users
			->where('email', Input::get('email'))
			->first();

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
		if (!$user) {
			return $this->redirectAction('reminder')
				->withErrors(Lang::get('c::auth.user-notfound'));
		}

		$credentials = Input::only('email');
		$token = Input::get('token');
		$input = Input::only('password', 'password_confirmation');

		// @todo validate password

		if (Password::reset($credentials, $token, $newPassword)) {
			return $this->redirectAction('login')
				->with('success', Lang::get('c::auth.reset-success'));
		} else {
			return $this->redirectAction('login')
				->withErrors(Lang::get('c::auth.reset-failure'));
		}
	}
}
