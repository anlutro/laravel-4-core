<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

use c\Auth\UserRepository;
use c\Auth\Activation\Activation;

/**
 * Controller for authentication actions.
 */
class AuthController extends \c\Controller
{
	/**
	 * @var \c\Auth\UserRepository
	 */
	protected $users;

	/**
	 * @param \c\Auth\UserRepository $users
	 */
	public function __construct(UserRepository $users)
	{
		$this->users = $users;

		$this->beforeFilter(function() {
			if (!$this->activationEnabled()) {
				throw new \RuntimeException('Activation/registration is not enabled');
			}
		}, ['only' => ['register', 'attemptRegistration', 'activate']]);

		$this->beforeFilter(function() {
			if (!$this->remindersEnabled()) {
				throw new \RuntimeException('Reminders are not enabled');
			}
		}, ['only' => ['reminder', 'sendReminder', 'reset', 'attemptReset']]);
	}

	/**
	 * Show the login form.
	 *
	 * @return View
	 */
	public function login()
	{
		$viewData = [
			'formAction' => $this->url('attemptLogin'),
		];

		if ($this->remindersEnabled()) {
			$viewData['resetUrl'] = $this->url('reminder');
		}
		
		return View::make('c::auth.login', $viewData);
	}

	/**
	 * Attempt to log a user in.
	 *
	 * @return Redirect
	 */
	public function attemptLogin()
	{
		$credentials = [
			'username'  => Input::get('username'),
			'password'  => Input::get('password'),
			'is_active' => 1,
		];

		if (Auth::attempt($credentials)) {
			// rehash password if necessary
			if (Hash::needsRehash(Auth::user()->getAuthPassword())) {
				$this->users->rehashPassword(Auth::user(), $credentials['password']);
			}

			$url = Config::get('c::redirect-login');

			return Redirect::intended($url)
				->with('success', Lang::get('c::auth.login-success'));
		} else {
			return $this->redirect('login')
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
		return $this->redirect('login')
			->with('info', Lang::get('c::auth.logout-success'));
	}

	/**
	 * Show the form for user registration.
	 *
	 * @return View
	 */
	public function register()
	{
		return View::make('c::auth.register', [
			'user'       => $this->users->getNew(),
			'formAction' => $this->url('attemptRegistration'),
		]);
	}

	/**
	 * Process a registration attempt.
	 *
	 * @return Redirect
	 */
	public function attemptRegistration()
	{
		$input = Input::all();

		if ($this->users->create($input)) {
			return $this->redirect('login')
				->with('success', Lang::get('c::auth.register-success'));
		} else {
			return $this->redirect('register')
				->withErrors($this->users->errors())
				->withInput();
		}
	}

	/**
	 * Process an activation attempt.
	 *
	 * @return Redirect
	 */
	public function activate()
	{
		$code = Input::get('activation_code');

		if (Activation::activate($code)) {
			$msg = Lang::get('c::auth.activation-success');
			return $this->redirect('login')->with('success', $msg);
		} else {
			return $this->redirect('login')
				->withErrors(Lang::get('c::auth.activation-failed'));
		}
	}

	/**
	 * Generate the form for sending a password reset token.
	 *
	 * @return View
	 */
	public function reminder()
	{
		return View::make('c::auth.reminder', [
			'formAction' => $this->url('sendReminder'),
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
		$user = $this->users->getByCredentials($credentials);

		if (!$user) {
			return $this->redirect('reminder')
				->withErrors(Lang::get('c::auth.user-email-notfound'));
		}

		if (Password::requestReset($user)) {
			return $this->redirect('login')
				->with('info', Lang::get('c::auth.resetpass-sent'));
		} else {
			return $this->redirect('reminder')
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
		if (!Input::get('token')) {
			return $this->redirect('login');
		}

		return View::make('c::auth.reset', [
			'formAction' => $this->url('attemptReset'),
			'token'      => Request::query('token'),
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
			return $this->redirect('reset', ['token' => $token])
				->withErrors($validator);
		}

		$redirect = $this->redirect('login');

		if (!$user = $this->users->getByCredentials($credentials)) {
			return $redirect->withErrors(Lang::get('reminders.user'));
		}

		$newPassword = Input::get('password');

		if (Password::resetUser($user, $token, $newPassword)) {
			return $redirect->with('success', Lang::get('c::auth.reset-success'));
		} else {
			return $redirect->withErrors(Lang::get('reminders.token'));
		}
	}

	/**
	 * Check if password reminders are enabled.
	 *
	 * @return boolean
	 */
	private function activationEnabled()
	{
		$loaded = App::getLoadedProviders();
		$provider = 'c\Auth\Activation\ActivationServiceProvider';
		return isset($loaded[$provider]);
	}

	/**
	 * Check if password reminders are enabled.
	 *
	 * @return boolean
	 */
	private function remindersEnabled()
	{
		$loaded = App::getLoadedProviders();
		$provider = 'c\Auth\Reminders\ReminderServiceProvider';
		return isset($loaded[$provider]);
	}
}
