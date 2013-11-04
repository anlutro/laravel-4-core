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
	}

	public function filter($filter)
	{
		$this->filter = $filter;
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
			if (strpos($key, 'password') !== false) $query->where($key, '=', $value);
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
		$types = $this->model->getDistinctUserTypes();
		$strings = [];

		foreach ($types as $type) {
			$strings[$type] = Lang::get('c::auth.usertype-'.$type);
		}

		return $strings;
	}

	public function create(array $attributes = array(), $activate = false)
	{
		$user = parent::create($attributes);

		if ($activate || (isset($attributes['active']) && $attributes['active'])) {
			$user->activate();
		} else {
			Activation::generate($user);
		}
		
		return $model;
	}

	public function activate($activationCode)
	{
		// should trigger on null, false, empty string
		if (!$activationCode) {
			throw new \InvalidArgumentException;
		}

		return Activation::activate($activationCode);
	}

	public function update(Model $model, array $attributes)
	{
		if (isset($attributes['password']) && $attributes['password'] == '') {
			unset($attributes['password']);
		}

		return parent::update($model, $attributes);
	}

	public function updateProfile(Model $model, array $attributes)
	{
		$this->validator->setKey($model->getKey());

		if (!$this->validator->validProfileUpdate($attributes, $model->getKey()))
			return false;
		
		return $this->update($model, $attributes);
	}

	public function processBulkAction($action, $keys)
	{
		$method = 'executeBulk' . ucfirst($action);
		if (!method_exists($this, $method))
			return;

		$query = $this->model
			->whereIn($this->model->getKeyName(), $keys);

		$this->$method($query);
	}

	protected function executeBulkDelete($query)
	{
		$query->delete();
	}
}
