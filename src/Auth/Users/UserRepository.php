<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Users;

use anlutro\LaravelRepository\EloquentRepository;

/**
 * Repository for user models.
 */
class UserRepository extends EloquentRepository
{
	protected $withSoftDeleted;

	public function __construct(UserModel $model, UserValidator $validator)
	{
		parent::__construct($model, $validator);
	}

	public function withSoftDeleted()
	{
		$this->withSoftDeleted = true;
	}

	public function search($search)
	{
		$this->pushCriteria(new SearchCriteria($search));

		return $this;
	}

	public function filter($type)
	{
		$this->pushCriteria(new TypeCriteria($type));

		return $this;
	}

	/**
	 * Get a user by his/her credentials.
	 *
	 * @param  array  $credentials
	 *
	 * @return null|\Illuminate\Database\Eloquent\Model
	 */
	public function findByCredentials(array $credentials)
	{
		foreach ($credentials as $key => $value) {
			if (strpos($key, 'password') !== false) {
				unset($credentials[$key]);
			}
		}

		if (!empty($credentials)) return $this->findByAttributes($credentials);
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
		$user = $this->getNew();

		// set the user level
		if (!empty($attributes['user_level'])) {
			$user->user_level = $attributes['user_level'];
		} elseif (!empty($attributes['user_type'])) {
			$user->user_type = $attributes['user_type'];
		}

		// either activate directly or send an activation code
		if (isset($attributes['is_active']) && $attributes['is_active']) {
			$user->is_active = true;
		}

		return $this->perform('create', $user, $attributes);
	}

	/**
	 * Update a user as an admin.
	 *
	 * @param  UserModel  $user
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
			$user->is_active = true;
		} else {
			$user->is_active = false;
		}

		return parent::update($user, $attributes);
	}

	public function validPasswordReset(array $attributes)
	{
		$result = $this->validator->validPasswordReset($attributes);

		if ($result === false) {
			$this->errors->merge($this->validator->getErrors());
		}

		return $result;
	}

	public function restore($user)
	{
		if (!$user->deleted_at) {
			throw new \InvalidArgumentException("Cannot restore user that has not been soft deleted");
		}

		$user->restore();

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function performCreate($user, array $attributes)
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
	protected function performUpdate($user, array $attributes)
	{
		if (isset($attributes['password']) && empty($attributes['password'])) {
			unset($attributes['password']);
		}

		return parent::performUpdate($user, $attributes);
	}

	protected function beforeQuery($query, $many)
	{
		if ($this->withSoftDeleted) {
			$query->withTrashed();
		}
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

		$query = $this->newQuery()
			->whereIn($this->model->getKeyName(), $keys);

		$this->$method($query);
	}

	protected function executeBulkDelete($query)
	{
		$query->delete();
	}

	protected function executeBulkRestore($query)
	{
		$query->restore();
	}

	protected function executeBulkActivate($query)
	{
		$query->update(['is_active' => true]);
	}

	protected function executeBulkDeactivate($query)
	{
		$query->update(['is_active' => false]);
	}
}
