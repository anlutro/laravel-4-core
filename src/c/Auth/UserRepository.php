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
	 * Prepare the query. This function is called before every getAll() and
	 * other functions that utilize $this->runQuery()
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
		$query = $this->model->newQuery();

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
	 * Create a new user.
	 *
	 * @param  array   $attributes
	 * @param  boolean $activate
	 *
	 * @return false|Model
	 */
	public function makeNew(array $attributes = array())
	{
		if (!$this->valid('create', $attributes)) {
			return false;
		}

		$user = $this->getNew($attributes);
		$user->username = $attributes['username'];
		$user->user_type = $attributes['user_type'];
		$this->prepareCreate($user);

		if (!$this->readyForSave($user) || !$this->readyForCreate($user)) {
			return false;
		}

		return $user;
	}

	public function create(array $attributes = array(), $activate = true)
	{
		if (!$user = $this->makeNew($attributes)) {
			return false;
		}
		
		if ($activate) {
			$user->activate();
		} else {
			Activation::generate($user);
		}
		
		return $user->save() ? $user : false;
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
	 * Dry update an existing user.
	 *
	 * @param  Model  $user
	 * @param  array  $attributes
	 *
	 * @return boolean
	 */
	public function dryUpdate($user, array $attributes, $action = 'update')
	{
		if (isset($attributes['password']) && $attributes['password'] == '') {
			unset($attributes['password']);
		}

		if (!parent::dryUpdate($user, $attributes)) {
			return false;
		}

		if (!empty($attributes['username'])) {
			$user->username = $attributes['username'];
		}
		
		if (!empty($attributes['user_type'])) {
			$user->user_type = $attributes['user_type'];
		}

		if (isset($attributes['is_active']) && (bool) $attributes['is_active'] !== false) {
			$user->activate();
		} else {
			$user->deactivate();
		}

		return $user;
	}

	/**
	 * Update a user's profile.
	 *
	 * @param  Model  $user
	 * @param  array  $attributes
	 *
	 * @return boolean
	 */
	public function updateProfile($user, array $attributes)
	{
		if (isset($attributes['password']) && $attributes['password'] == '') {
			unset($attributes['password']);
		}

		return parent::dryUpdate($user, $attributes, 'profileUpdate') ? $user->save() : false;
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
