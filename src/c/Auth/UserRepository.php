<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use c\Auth\Activation\Activation;

/**
 * Repository for user models.
 */
class UserRepository extends \c\EloquentRepository
{
	protected $model;
	protected $validator;
	protected $search;
	protected $filter;

	public function __construct(UserModel $model, UserValidator $validator)
	{
		parent::__construct($model, $validator);
	}

	public function search($search)
	{
		$this->search = $search;

		return $this;
	}

	public function filter($filter)
	{
		$this->filter = $filter;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function prepareQuery($query, $many)
	{
		if ($this->search) {
			$query->searchFor($this->search);
		}

		if ($this->filter) {
			$query->whereUserType($this->filter);
		}
	}

	/**
	 * We'll use the auth driver to get the currently logged in user.
	 */
	public function getCurrentUser()
	{
		return Auth::user();
	}

	/**
	 * Get a user by his/her credentials.
	 *
	 * @param  array  $credentials
	 *
	 * @return null|Model
	 */
	public function getByCredentials(array $credentials)
	{
		$query = $this->newQuery();

		foreach ($credentials as $key => $value) {
			if (strpos($key, 'password') === false) {
				$query->where($key, '=', $value);
			}
		}

		return $this->fetchSingle($query);
	}

	/**
	 * Get a list of unique user types.
	 *
	 * @return array
	 */
	public function getUserTypes()
	{
		$types = array_flip($this->model->getAccessLevels());
		$strings = [];

		foreach ($types as $type) {
			if (!empty($type)) {
				$strings[$type] = Lang::get('c::user.usertype-'.$type);
			}
		}

		return $strings;
	}

	/**
	 * {@inheritdoc}
	 */
	public function prepareCreate($user, $attributes)
	{
		// set the username manually as it is not fillable
		$user->username = $attributes['username'];

		// set the user level
		if (isset($attributes['user_type']) && !empty($attributes['user_type'])) {
			$user->user_type = $attributes['user_type'];
		} elseif (isset($attributes['user_level']) && !empty($attributes['user_level'])) {
			$user->user_level = $attributes['user_level'];
		}

		// either activate directly or send an activation code
		if (isset($attributes['is_active']) && $attributes['is_active']) {
			$user->activate();
		} else {
			Activation::generate($user);
		}
	}

	/**
	 * Rehash a user's password.
	 *
	 * @param  c\Auth\UserModel  $user
	 * @param  string  $password
	 *
	 * @return boolean
	 */
	public function rehashPassword(UserModel $user, $password)
	{
		$user->password = $password;
		return $user->save();
	}

	/**
	 * Activate user with a certain activation code.
	 *
	 * @param  string $activationCode
	 *
	 * @return boolean
	 */
	public function activate($activationCode)
	{
		if (empty($activationCode)) {
			throw new \InvalidArgumentException('Activation code missing');
		}

		return Activation::activate($activationCode);
	}

	/**
	 * Directly activate a user.
	 *
	 * @param  UserModel $user
	 *
	 * @return boolean
	 */
	public function activateUser(UserModel $user)
	{
		return $user->activate();
	}

	/**
	 * {@inheritdoc}
	 */
	public function dryUpdate($user, array $attributes, $action = 'update')
	{
		if (isset($attributes['password']) && empty($attributes['password'])) {
			unset($attributes['password']);
		}

		return parent::dryUpdate($user, $attributes, $action);
	}

	/**
	 * Update a user as an admin.
	 *
	 * @param  c\Auth\UserModel  $user
	 * @param  array  $attributes
	 *
	 * @return boolean
	 */
	public function updateAsAdmin($user, array $attributes)
	{
		if (!$this->dryUpdate($user, $attributes)) {
			return false;
		}

		if (isset($attributes['username']) && !empty($attributes['username'])) {
			$user->username = $attributes['username'];
		}
		
		if (isset($attributes['user_type']) && !empty($attributes['user_type'])) {
			$user->user_type = $attributes['user_type'];
		} elseif (isset($attributes['user_level']) && !empty($attributes['user_level'])) {
			$user->user_level = $attributes['user_level'];
		}

		if (isset($attributes['is_active']) && (bool) $attributes['is_active'] !== false) {
			$user->activate();
		} else {
			$user->deactivate();
		}

		return $user->save();
	}

	/**
	 * Process a bulk action on a set of users.
	 *
	 * @param  string $action
	 * @param  array  $keys
	 *
	 * @return void
	 */
	public function processBulkAction($action, $keys)
	{
		$method = 'executeBulk' . ucfirst($action);
		if (!method_exists($this, $method)) {
			throw new \InvalidArgumentException("Invalid bulk action: $action ($method does not exist)");
		}

		$query = $this->model
			->whereIn($this->model->getKeyName(), $keys);

		$this->$method($query);
	}

	/**
	 * Bulk delete users.
	 *
	 * @param  Builder $query
	 *
	 * @return void
	 */
	protected function executeBulkDelete($query)
	{
		$query->delete();
	}
}
