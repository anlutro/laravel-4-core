<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth;

use anlutro\LaravelRepository\EloquentRepository;

/**
 * Repository for user models.
 */
class UserRepository extends EloquentRepository
{
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
	protected function beforeQuery($query, $many)
	{
		if ($this->search) {
			$query->searchFor($this->search);
		}

		if ($this->filter) {
			$query->whereUserType($this->filter);
		}
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
		return array_flip($this->model->getAccessLevels());
	}

	/**
	 * Create a user as an admin.
	 */
	public function createAsAdmin(array $attributes)
	{
		$user = $this->getNew($attributes);

		// set the user level
		if (!empty($attributes['user_level'])) {
			$user->user_level = $attributes['user_level'];
		} elseif (!empty($attributes['user_type'])) {
			$user->user_type = $attributes['user_type'];
		}

		// either activate directly or send an activation code
		if (isset($attributes['is_active']) && $attributes['is_active']) {
			$this->activateUser($user);
		}

		return $this->perform('create', $user, $attributes);
	}

	/**
	 * Update a user as an admin.
	 *
	 * @param  anlutro\Core\Auth\UserModel  $user
	 * @param  array  $attributes
	 *
	 * @return boolean
	 */
	public function updateAsAdmin($user, array $attributes)
	{
		if (isset($attributes['password']) && empty($attributes['password'])) {
			unset($attributes['password']);
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

		return parent::update($user, $attributes);
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
	public function performCreate($user, array $attributes)
	{
		// set the username manually as it is not fillable
		$user->username = $attributes['username'];

		// set a default user level if not set already
		if (!$user->user_level) $user->user_level = 1;

		return parent::performCreate($user, $attributes);
	}

	/**
	 * {@inheritdoc}
	 */
	public function performUpdate($user, array $attributes)
	{
		if (isset($attributes['password']) && empty($attributes['password'])) {
			unset($attributes['password']);
		}

		return parent::performUpdate($user, $attributes);
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
