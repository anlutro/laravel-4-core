<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Web;

use anlutro\LaravelController\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

use anlutro\Core\Auth\UserManager;
use anlutro\Core\Auth\Activation\ActivationException;

/**
 * Controller for authentication actions.
 */
class AuthController extends Controller
{
	/**
	 * @var \anlutro\Core\Auth\UserManager
	 */
	protected $users;

	/**
	 * @param \anlutro\Core\Auth\UserManager $users
	 */
	public function __construct(UserManager $users)
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
	 * @return \Illuminate\View\View
	 */
	public function login()
	{
		$viewData = [
			'formAction' => $this->url('attemptLogin'),
		];

		if ($this->remindersEnabled()) {
			$viewData['resetUrl'] = $this->url('reminder');
		}
		
		return $this->view('c::auth.login', $viewData);
	}

	/**
	 * Attempt to log a user in.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function attemptLogin()
	{
		$credentials = [
			'username'  => $this->input('username'),
			'password'  => $this->input('password'),
		];

		if ($this->users->login($credentials)) {
			$url = Config::get('c::redirect-login', '/');
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
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function logout()
	{
		$this->users->logout();
		return $this->redirect('login')
			->with('info', Lang::get('c::auth.logout-success'));
	}

	/**
	 * Show the form for user registration.
	 *
	 * @return \Illuminate\View\View
	 */
	public function register()
	{
		return $this->view('c::auth.register', [
			'user'       => $this->users->getNew(),
			'formAction' => $this->url('attemptRegistration'),
		]);
	}

	/**
	 * Process a registration attempt.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function attemptRegistration()
	{
		$input = $this->input();

		if ($this->users->register($input)) {
			return $this->redirect('login')
				->with('success', Lang::get('c::auth.register-success'));
		} else {
			return $this->redirect('register')
				->withErrors($this->users->getErrors())
				->withInput();
		}
	}

	/**
	 * Process an activation attempt.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function activate()
	{
		$code = $this->input('activation_code');

		try {
			$this->users->activateByCode($code);
			$msg = Lang::get('c::auth.activation-success');
			return $this->redirect('login')->with('success', $msg);
		} catch (ActivationException $e) {
			if (Config::get('app.debug')) throw $e;
			return $this->redirect('login')
				->withErrors(Lang::get('c::auth.activation-failed'));
		}
	}

	/**
	 * Generate the form for sending a password reset token.
	 *
	 * @return \Illuminate\View\View
	 */
	public function reminder()
	{
		return $this->view('c::auth.reminder', [
			'formAction' => $this->url('sendReminder'),
		]);
	}

	/**
	 * Send a password reset token.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function sendReminder()
	{
		if ($this->users->requestPasswordResetForEmail($this->input('email'))) {
			return $this->redirect('login')
				->with('info', Lang::get('c::auth.reminder-sent'));
		} else {
			return $this->redirect('reminder')
				->withErrors(Lang::get('c::std.failure'));
		}
	}

	/**
	 * Show the form for finalizing a password reset.
	 *
	 * @return \Illuminate\View\View
	 */
	public function reset()
	{
		if (!$this->input('token')) {
			return $this->redirect('login');
		}

		return $this->view('c::auth.reset', [
			'formAction' => $this->url('attemptReset'),
			'token'      => Request::query('token'),
		]);
	}

	/**
	 * Attempt to reset a user's password.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function attemptReset()
	{
		$credentials = $this->input(['username']);
		$token = $this->input('token');
		$input = $this->input(['password', 'password_confirmation']);

		if ($this->users->resetPasswordForCredentials($credentials, $input, $token)) {
			return $this->redirect('login')
				->with('success', Lang::get('c::auth.reset-success'));
		} else {
			return $this->redirect('login')
				->withErrors(Lang::get('reminders.token'));
		}
	}

	/**
	 * Check if password reminders are enabled.
	 *
	 * @return boolean
	 */
	private function activationEnabled()
	{
		return $this->users->activationsEnabled();
	}

	/**
	 * Check if password reminders are enabled.
	 *
	 * @return boolean
	 */
	private function remindersEnabled()
	{
		return $this->users->remindersEnabled();
	}
}
