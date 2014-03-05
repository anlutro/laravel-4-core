<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c\Auth;

use Illuminate\Auth\AuthManager;
use Illuminate\Translation\Translator;

use c\Auth\Activation\ActivationService;
use c\Auth\Reminders\PasswordBroker;

/**
 * Top-level user service class.
 */
class UserManager
{
	protected $users;
	protected $auth;
	protected $translator;
	protected $activations;
	protected $reminders;

	/**
	 * @param UserRepository $users
	 * @param AuthManager    $auth
	 * @param Translator     $translator
	 */
	public function __construct(
		UserRepository $users,
		AuthManager $auth,
		Translator $translator
	) {
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
		return $this->auth->getUser();
	}

	/**
	 * Create a new user.
	 *
	 * @param  array  $attributes
	 *
	 * @return UserModel|false
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

		if (!$user = $this->users->createAsAdmin($attributes)) return false;

		if (!$user->is_active && !empty($attributes['send_activation'])) {
			$this->sendActivationCode($user);
		}

		return $user;
	}

	/**
	 * Register a new user and send an activation code.
	 *
	 * @param  array  $attributes
	 *
	 * @return UserModel|false
	 */
	public function register(array $attributes)
	{
		if (!$user = $this->users->create($attributes)) return false;

		$this->sendActivationCode($user);

		return $user;
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
		$user = $this->getCurrentUser();

		if (empty($attributes['old_password']) || !$user->confirmPassword($attributes['old_password'])) {
			$this->users->getErrors()->add($this->translate('c::auth.invalid-password'));
			return false;
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

		return $this->users->delete($user, $attributes);
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
			$against = $against->user_level;
		}

		if (is_string($against)) {
			$types = $this->users->getUserTypes();
			$against = $types[$against];
		}

		if (!is_numeric($against)) {
			throw new \InvalidArgumentException("Invalid permission check: $against");
		}

		$level = (int) $this->getCurrentUser()->user_level;

		if ($level < (int) $against) {
			throw new AccessDeniedException;
		}
	}

	/**
	 * Given correct credentials, log a user in.
	 *
	 * @param  array  $credentials
	 *
	 * @return bool
	 */
	public function login(array $credentials)
	{
		$credentials['is_active'] = 1;

		if ($this->auth->attempt($credentials)) {
			$this->getCurrentUser()->rehashPassword($credentials['password']);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Log out the current user.
	 *
	 * @return void
	 */
	public function logout()
	{
		return $this->auth->logout();
	}

	/**
	 * Send an activation code to a user.
	 *
	 * @param  UserModel $user
	 *
	 * @return bool
	 */
	public function sendActivationCode($user)
	{
		if ($this->activations === null) {
			throw new \RuntimeException('Activation service not set.');
		}

		return $this->activations->generate($user);
	}

	/**
	 * Given an activation code, activate the user.
	 *
	 * @param  string $code
	 *
	 * @return bool
	 */
	public function activateByCode($code)
	{
		if ($this->activations === null) {
			throw new \RuntimeException('Activation service not set.');
		}
		
		return $this->activations->activate($code);
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

		$user = $this->users->getByCredentials(['email' => $email]);

		if (!$user) return false;

		return $this->reminders->requestReset($user);
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

		if (!$user = $this->users->getByCredentials($credentials)) return false;

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

		if (!$this->users->valid('passwordReset', $attributes)) return false;

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
