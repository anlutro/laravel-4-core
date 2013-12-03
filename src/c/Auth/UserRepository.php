<?php
/**
 * Laravel 4 Core - User repository
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use c\Auth\Activation\Activation;

class UserRepository extends \c\EloquentRepository
{
	protected $model;
	protected $validator;
	protected $search;
	protected $filter;

	public function __construct(UserModel $model, UserValidator $validator)
	{
		$this->model = $model;
		$this->validator = $validator;
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
	 * Prepare the query. This function is called before every getAll() and
	 * other functions that utilize $this->runQuery()
	 */
	protected function prepareQuery($query)
	{
		if ($this->search) {
			$query->searchFor($this->search);
		}

		if ($this->filter) {
			$query->filterUserType($this->filter);
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
		$query = $this->model->newQuery();

		foreach ($credentials as $key => $value) {
			if (strpos($key, 'password') === false) {
				$query->where($key, '=', $value);
			}
		}

		return $query->first();
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
	 * Create a new user.
	 *
	 * @param  array   $attributes
	 * @param  boolean $activate
	 *
	 * @return false|Model
	 */
	public function create(array $attributes = array(), $activate = false)
	{
		if (!$this->validator->validCreate($attributes)) {
			return false;
		}

		$user = $this->getNew($attributes);
		$user->username = $attributes['username'];
		$user->user_type = $attributes['user_type'];

		if ($activate) {
			$user->activate();
		} else {
			Activation::generate($user);
		}
		
		return $user;
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
			throw new \InvalidArgumentException;
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
	 * Update an existing user.
	 *
	 * @param  Model  $model
	 * @param  array  $attributes
	 *
	 * @return boolean
	 */
	public function update(Model $model, array $attributes)
	{
		if (isset($attributes['password']) && $attributes['password'] == '') {
			unset($attributes['password']);
		}

		if (!$model->exists) {
			throw new \RuntimeException('Cannot update non-existing model');
		}

		$this->validator->setKey($model->getKey());
		
		if (!$this->validator->validUpdate($attributes)) {
			return false;
		}

		$model->fill($attributes);
		$model->username = $attributes['username'];
		$model->user_type = $attributes['user_type'];

		if (isset($attributes['is_active']) && $attributes['is_active'] !== false) {
			return $model->activate();
		} else {
			return $model->deactivate();
		}
	}

	/**
	 * Update a user's profile.
	 *
	 * @param  Model  $model
	 * @param  array  $attributes
	 *
	 * @return boolean
	 */
	public function updateProfile(Model $model, array $attributes)
	{
		if (isset($attributes['password']) && $attributes['password'] == '') {
			unset($attributes['password']);
		}

		$this->validator->setKey($model->getKey());

		if (!$this->validator->validProfileUpdate($attributes, $model->getKey())) {
			return false;
		}
		
		return $model->update($attributes);
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
			throw new \InvalidArgumentException('');
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
