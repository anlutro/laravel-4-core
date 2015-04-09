<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth;

use Illuminate\Auth\AuthManager;
use Illuminate\Database\Connection;
use Illuminate\Translation\Translator;
use anlutro\LaravelValidation\ValidationException;

use anlutro\Core\Auth\Activation\ActivationService;
use anlutro\Core\Auth\Reminders\PasswordBroker;
use anlutro\Core\Auth\Users\UserModel;
use anlutro\Core\Auth\Users\UserRepository;

/**
 * Top-level user service class.
 */
class UserManager
{
	/**
	 * @var Connection
	 */
	protected $db;

	/**
	 * @var UserRepository
	 */
	protected $users;

	/**
	 * @var AuthManager|\Illuminate\Auth\Guard
	 */
	protected $auth;

	/**
	 * @var Translator
	 */
	protected $translator;

	/**
	 * @var ActivationService
	 */
	protected $activations;

	/**
	 * @var PasswordBroker
	 */
	protected $reminders;

	/**
	 * @param Connection     $db
	 * @param UserRepository $users
	 * @param AuthManager    $auth
	 * @param Translator     $translator
	 */
	public function __construct(
		Connection $db,
		UserRepository $users,
		AuthManager $auth,
		Translator $translator
	) {
		$this->db = $db;
		$this->users = $users;
		$this->auth = $auth;
		$this->translator = $translator;
	}

	/**
	 * Set the activation service.
	 *
	 * @param ActivationService $activations
	 */
	public function setActivationService(ActivationService $activations)
	{
		$this->activations = $activations;
	}

	/**
	 * Check if activations are enabled.
	 *
	 * @return bool
	 */
	public function activationsEnabled()
	{
		return $this->activations !== null;
	}

	/**
	 * Set the password reminder service.
	 *
	 * @param PasswordBroker $reminders
	 */
	public function setReminderService(PasswordBroker $reminders)
	{
		$this->reminders = $reminders;
	}

	/**
	 * Check if password reminders are enabled.
	 *
	 * @return bool
	 */
	public function remindersEnabled()
	{
		return $this->reminders !== null;
	}

	/**
	 * Set the current user.
	 *
	 * @param UserModel $user
	 */
	public function setCurrentUser(UserModel $user)
	{
		$this->auth->setUser($user);
	}

	/**
	 * Get the current user.
	 *
	 * @return UserModel
	 */
	public function getCurrentUser()
	{
		return $this->auth->user();
	}

	/**
	 * Create a new user.
	 *
	 * @param  array  $attributes
	 *
	 * @return UserModel
	 */
	public function create(array $attributes)
	{
		if (!empty($attributes['user_level'])) {
			$level = (int) $attributes['user_level'];
		} elseif (!empty($attributes['user_type'])) {
			$level = (string) $attributes['user_type'];
		} else {
			$level = 1;
		}

		$this->checkPermissions($level);

		$this->users->toggleExceptions(true);

		return $this->db->transaction(function() use($attributes) {
			$user = $this->users->createAsAdmin($attributes);

			if (!$user->is_active && !empty($attributes['send_activation'])) {
				$this->sendActivationCode($user);
			}

			return $user;
		});
	}

	/**
	 * Register a new user and send an activation code.
	 *
	 * @param  array  $attributes
	 *
	 * @return UserModel
	 */
	public function register(array $attributes)
	{
		$this->users->toggleExceptions(true);

		return $this->db->transaction(function() use($attributes) {
			$user = $this->users->create($attributes);

			$this->sendActivationCode($user);

			return $user;
		});
	}

	/**
	 * Update the current user's profile.
	 *
	 * @param  array $attributes
	 *
	 * @return bool
	 */
	public function updateCurrentProfile(array $attributes)
	{
		$this->users->toggleExceptions(true);

		$user = $this->getCurrentUser();

		if (empty($attributes['old_password']) || !$user->confirmPassword($attributes['old_password'])) {
			throw new ValidationException(['password' => $this->translate('c::auth.invalid-password')]);
		}

		return $this->users->update($user, $attributes);
	}

	/**
	 * Update a user as an admin.
	 *
	 * @param  UserModel $user
	 * @param  array     $attributes
	 *
	 * @return bool
	 */
	public function updateAsAdmin(UserModel $user, array $attributes)
	{
		$this->checkPermissions($user);

		$this->users->toggleExceptions(true);

		return $this->users->updateAsAdmin($user, $attributes);
	}

	/**
	 * Delete a user.
	 *
	 * @param  UserModel $user
	 *
	 * @return bool
	 */
	public function delete($user)
	{
		$this->checkPermissions($user);

		return $this->users->delete($user);
	}

	/**
	 * Check if the current user has permission to modify a user.
	 *
	 * @param  UserModel|int $against
	 *
	 * @return void
	 * 
	 * @throws AccessDeniedException
	 */
	public function checkPermissions($against)
	{
		if ($against instanceof UserModel) {
			$against = (int) $against->user_level;
		}

		if (!is_numeric($against)) {
			/** @var \anlutro\Core\Auth\Users\UserModel $user */
			$user = $this->users->getModel();
			$against = $user->getUserLevelValue($against);
		}

		$level = (int) $this->getCurrentUser()->user_level;

		if ($level < (int) $against) {
			throw new AccessDeniedException("Logged in user (level $level) cannot modify user with level $against");
		}
	}

	/**
	 * Determine if the current user has permission to modify a user.
	 *
	 * @param  UserModel|int $level
	 *
	 * @return boolean
	 */
	public function hasPermission($level)
	{
		try {
			$this->checkPermissions($level);
			return true;
		} catch (AccessDeniedException $e) {
			return false;
		}
	}

	/**
	 * Given correct credentials, log a user in.
	 *
	 * @param  array   $credentials
	 * @param  boolean $remember
	 *
	 * @return bool
	 * @throws AuthenticationException
	 */
	public function login(array $credentials, $remember = false)
	{
		$credentials['is_active'] = 1;

		// if the "eloquent-exceptions" driver is being used, an exception will
		// be thrown if authentication failed. if one of the stock drivers are
		// being used, it will just return false, and we have to throw the
		// exception ourselves, with less information.
		if (!$this->auth->attempt($credentials, $remember)) {
			throw new AuthenticationException('Illuminate\Auth\Guard::attempt returned false');
		}

		$this->getCurrentUser()->rehashPassword($credentials['password']);

		return true;
	}

	/**
	 * Log out the current user.
	 *
	 * @return void
	 */
	public function logout()
	{
		$this->auth->logout();
	}

	public function switchToUser($user, $remember = false)
	{
		$this->auth->login($user, $remember);
	}

	public function switchToUserId($userId, $remember = false)
	{
		$this->auth->loginUsingId($userId, $remember);
	}

	/**
	 * Send an activation code to a user.
	 *
	 * @param  UserModel $user
	 *
	 * @return void
	 */
	public function sendActivationCode($user)
	{
		if ($this->activations === null) {
			throw new \RuntimeException('Activation service not set.');
		}

		$this->activations->generate($user);
	}

	/**
	 * Given an activation code, activate the user.
	 *
	 * @param  string $code
	 *
	 * @return void
	 */
	public function activateByCode($code)
	{
		if ($this->activations === null) {
			throw new \RuntimeException('Activation service not set.');
		}
		
		$this->activations->activate($code);
	}

	/**
	 * Request a password reset for a given email.
	 *
	 * @param  string $email
	 *
	 * @return bool
	 */
	public function requestPasswordResetForEmail($email)
	{
		if ($this->reminders === null) {
			throw new \RuntimeException('Password reset service not set.');
		}

		return $this->db->transaction(function() use($email) {
			$user = $this->users->findByCredentials(['email' => $email]);

			if (!$user) {
				throw new Reminders\ReminderException;
			}

			return $this->reminders->requestReset($user);
		});
	}

	/**
	 * Request a password reset for a user.
	 *
	 * @param  UserModel $user
	 *
	 * @return bool
	 */
	public function requestPasswordReset($user)
	{
		if ($this->reminders === null) {
			throw new \RuntimeException('Password reset service not set.');
		}

		return $this->reminders->requestReset($user);
	}

	/**
	 * Reset the password for a user of given credentials.
	 *
	 * @param  array  $credentials username, email...
	 * @param  array  $attributes  password + confirmation
	 * @param  string $token       password reset token
	 *
	 * @return bool
	 */
	public function resetPasswordForCredentials(array $credentials, array $attributes, $token)
	{
		if ($this->reminders === null) {
			throw new \RuntimeException('Password reset service not set.');
		}

		$user = $this->users->findByCredentials($credentials);

		if (!$user) {
			throw new Reminders\ReminderException;
		}

		return $this->resetPassword($user, $attributes, $token);
	}

	/**
	 * Reset the password of a user.
	 *
	 * @param  UserModel $user
	 * @param  array     $attributes
	 * @param  string    $token
	 *
	 * @return bool
	 */
	public function resetPassword(UserModel $user, array $attributes, $token)
	{
		if ($this->reminders === null) {
			throw new \RuntimeException('Password reset service not set.');
		}

		$this->users->toggleExceptions(true);
		$this->users->validPasswordReset($attributes);

		$newPassword = $attributes['password'];

		return $this->reminders->resetUser($user, $token, $newPassword);
	}

	protected function translate($string)
	{
		return $this->translator->get($string);
	}

	public function __call($method, $args)
	{
		if (is_callable([$this->users, $method])) {
			return call_user_func_array([$this->users, $method], $args);
		}

		$class = get_class($this); $repo = get_class($this->users);
		throw new \BadMethodCallException("The method $method does not exist on this class ($class) or its $repo");
	}
}
