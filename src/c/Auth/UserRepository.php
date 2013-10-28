<?php
namespace c\Auth;

use anlutro\L4Base\EloquentRepository as BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class UserRepository extends BaseRepository
{
	protected $search;
	protected $filter;

	public function __construct(UserModel $model)
	{
		$this->model = $model;
	}

	public function prepareQuery($query)
	{
		if ($this->search) {
			$query->addSearchConstraint($this->search);
		}

		if ($this->filter) {
			$query->filterUserType($this->filter);
		}
	}

	public function getCurrentUser()
	{
		return Auth::user();
	}

	public function getUserTypes()
	{
		$types = $this->model->getDistinctUserTypes();
		$strings = [];

		foreach ($types as $type) {
			$strings[$type] = Lang::get('c::auth.usertype-'.$type);
		}

		return $strings;
	}

	public function updateUserProfile(UserModel $user, $attributes)
	{
		// @todo filter $attributes
		return $this->update($user, $attributes);
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
