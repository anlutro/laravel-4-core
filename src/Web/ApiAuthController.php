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

use anlutro\Core\Auth\UserManager;

/**
 * Controller for authentication actions.
 */
class ApiAuthController extends ApiController
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
			if (!$this->users->activationEnabled()) {
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

		if ($this->users->login($credentials)) {
			$user = $this->users->getCurrentUser();
			$data = ['status' => 'logged in', 'user' => $user];
			return $this->jsonResponse($data, 200);
		} else {
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
		if ($user = $this->users->register($this->input())) {
			$data = ['status' => 'registered', 'user' => $user];
			return $this->jsonResponse($data, 200);
		} else {
			return $this->error($this->users->getErrors());
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

		if ($this->users->activateByCode($code)) {
			return $this->success();
		} else {
			return $this->error(['actiavtion failed']);
		}
	}

	/**
	 * Send a password reset token.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function sendReminder()
	{
		if ($this->users->requestPasswordResetForEmail($this->input('email'))) {
			return $this->success(['reminder email sent']);
		} else {
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

		if ($this->users->resetPasswordForCredentials($credentials, $input, $token)) {
			return $this->success(['password reset, please log in']);
		} else {
			$errors = array_merge($this->users->getErrors(), ['password could not be reset']);
			return $this->error($errors);
		}
	}
}
