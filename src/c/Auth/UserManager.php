<?php
namespace c\Auth;

use Illuminate\Auth\AuthManager;
use Illuminate\Translation\Translator;

use c\Auth\Activation\ActivationService;
use c\Auth\Reminders\PasswordBroker;

class UserManager
{
	protected $users;
	protected $auth;
	protected $translator;
	protected $activations;
	protected $reminders;

	public function __construct(
		UserRepository $users,
		AuthManager $auth,
		Translator $translator
	) {
		$this->users = $users;
		$this->auth = $auth;
		$this->translator = $translator;
	}

	public function setActivationService(ActivationService $activations)
	{
		$this->activations = $activations;
	}

	public function activationsEnabled()
	{
		return $this->activations !== null;
	}

	public function setReminderService(PasswordBroker $reminders)
	{
		$this->reminders = $reminders;
	}

	public function remindersEnabled()
	{
		return $this->reminders !== null;
	}

	public function setCurrentUser($user)
	{
		$this->auth->setUser($user);
	}

	public function getCurrentUser()
	{
		return $this->auth->getUser();
	}

	public function create(array $attributes)
	{
		if (!empty($attributes['user_level'])) {
			$level = $attributes['user_level'];
		} else {
			$types = $this->users->getUserTypes();

			if (!empty($attributes['user_type'])) {
				$type = $attributes['user_type'];

				if (!array_key_exists($type, $types)) {
					throw new \InvalidArgumentException("Invalid type: $type");
				}

				$level = $types[$type];
			} else {
				$types = array_flip($types);
				$level = $types['user'];
			}
		}

		$this->checkPermissions($level);

		if (!$user = $this->users->create($attributes)) return false;

		if (!$user->is_active && !empty($attributes['send_activation'])) {
			$this->sendActivationCode($user);
		}

		return $user;
	}

	public function register(array $attributes)
	{
		unset($attributes['user_level']);
		unset($attributes['user_type']);
		unset($attributes['is_active']);

		$user = $this->users->create($attributes);

		$this->sendActivationCode($user);

		return $user;
	}

	public function updateCurrentProfile($attributes)
	{
		$user = $this->getCurrentUser();

		if (empty($attributes['old_password']) || !$user->confirmPassword($attributes['old_password'])) {
			$this->users->getErrors()->add($this->translate('c::auth.invalid-password'));
			return false;
		}

		return $this->users->update($user, $attributes);
	}

	public function updateAsAdmin($user, $attributes)
	{
		$this->checkPermissions($user);

		return $this->users->updateAsAdmin($user, $attributes);
	}

	public function delete($user)
	{
		$this->checkPermissions($user);

		return $this->users->delete($user, $attributes);
	}

	public function checkPermissions($against)
	{
		if ($against instanceof UserModel) {
			$against = $against->user_level;
		}

		if (!is_numeric($against)) {
			throw new \InvalidArgumentException("Invalid permission check: $against");
		}

		$level = (int) $this->getCurrentUser()->user_level;

		if ($level < (int) $against) {
			throw new AccessDeniedException;
		}
	}

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

	public function logout()
	{
		return $this->auth->logout();
	}

	public function sendActivationCode($user)
	{
		if ($this->activations === null) {
			throw new \RuntimeException('Activation service not set.');
		}

		return $this->activations->generate($user);
	}

	public function activateByCode($code)
	{
		if ($this->activations === null) {
			throw new \RuntimeException('Activation service not set.');
		}
		
		return $this->activations->activate($code);
	}

	public function requestPasswordResetForEmail($email)
	{
		if ($this->reminders === null) {
			throw new \RuntimeException('Password reset service not set.');
		}

		$user = $this->users->getByCredentials(['email' => $email]);

		if (!$user) return false;

		return $this->reminders->requestReset($user);
	}

	public function requestPasswordReset($user)
	{
		if ($this->reminders === null) {
			throw new \RuntimeException('Password reset service not set.');
		}

		return $this->reminders->requestReset($user);
	}

	public function resetPasswordForCredentials(array $credentials, array $attributes, $token)
	{
		if ($this->reminders === null) {
			throw new \RuntimeException('Password reset service not set.');
		}

		if (!$user = $this->users->getByCredentials($credentials)) return false;

		return $this->resetPassword($user, $attributes, $token);
	}

	public function resetPassword($user, array $attributes, $token)
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

		$self = get_class($this); // $self = substr($self, strrpos($self, '\\')+1);
		$repo = get_class($this->users); // $repo = substr($repo, strrpos($repo, '\\')+1);
		throw new \BadMethodCallException("The method $method does not exist on this class ($class) or its $repo");
	}
}
