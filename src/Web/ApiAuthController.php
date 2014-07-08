<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Web;

use anlutro\LaravelController\ApiController;
use anlutro\LaravelValidation\ValidationException;
use Illuminate\Support\Facades\Config;

use anlutro\Core\Auth\AuthenticationException;
use anlutro\Core\Auth\Activation\ActivationException;
use anlutro\Core\Auth\Reminders\ReminderException;
use anlutro\Core\Auth\UserManager;

/**
 * Controller for authentication actions.
 */
class ApiAuthController extends ApiController
{
	/**
	 * @var boolean
	 */
	protected $debug;

	/**
	 * @var \anlutro\Core\Auth\UserManager
	 */
	protected $users;

	/**
	 * @param \anlutro\Core\Auth\UserManager $users
	 */
	public function __construct(UserManager $users)
	{
		$this->debug = (bool) Config::get('app.debug');
		$this->users = $users;

		$this->beforeFilter(function() {
			if (!$this->users->activationsEnabled()) {
				throw new \RuntimeException('Activation/registration is not enabled');
			}
		}, ['only' => ['attemptRegistration', 'activate']]);

		$this->beforeFilter(function() {
			if (!$this->users->remindersEnabled()) {
				throw new \RuntimeException('Reminders are not enabled');
			}
		}, ['only' => ['sendReminder', 'attemptReset']]);
	}

	/**
	 * Attempt to log a user in.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function attemptLogin()
	{
		$credentials = [
			'username'  => $this->input('username'),
			'password'  => $this->input('password'),
		];

		$remember = Config::get('c::login-remember') && $this->input('remember_me');

		try {
			$this->users->login($credentials, $remember);
			$user = $this->users->getCurrentUser();
			$data = ['status' => 'logged in', 'user' => $user];
			return $this->jsonResponse($data, 200);
		} catch (AuthenticationException $e) {
			if ($this->debug) throw $e;
			return $this->status('login failed', 401);
		}
	}

	/**
	 * Log out the currently logged in user.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function logout()
	{
		$this->users->logout();

		return $this->status('logged out', 403);
	}

	/**
	 * Process a registration attempt.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function attemptRegistration()
	{
		try {
			$user = $this->users->register($this->input());
			$data = ['status' => 'registered', 'user' => $user];
			return $this->jsonResponse($data, 200);
		} catch (ValidationException $e) {
			return $this->error($e->getErrors());
		} catch (ActivationException $e) {
			if ($this->debug) throw $e;
			return $this->error(['registration failed, please try again later']);
		}
	}

	/**
	 * Process an activation attempt.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function activate()
	{
		$code = $this->input('activation_code');

		try {
			$this->users->activateByCode($code);
			return $this->success();
		} catch (ActivationException $e) {
			if ($this->debug) throw $e;
			return $this->error(['activation failed']);
		}
	}

	/**
	 * Send a password reset token.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function sendReminder()
	{
		try {
			$this->users->requestPasswordResetForEmail($this->input('email'));
			return $this->success(['reminder email sent']);
		} catch (ReminderException $e) {
			return $this->error(['no user with that e-mail']);
		}
	}

	/**
	 * Attempt to reset a user's password.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function attemptReset()
	{
		$credentials = $this->input(['username']);
		$token = $this->input('token');
		$input = $this->input(['password', 'password_confirmation']);

		try {
			$this->users->resetPasswordForCredentials($credentials, $input, $token);
			return $this->success(['password reset, please log in']);
		} catch (ReminderException $e) {
			return $this->error(['password could not be reset']);
		}
	}
}
