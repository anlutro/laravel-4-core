<?php
namespace anlutro\Core\Auth\Users;

use anlutro\LaravelRepository\CriteriaInterface;

class SearchCriteria implements CriteriaInterface
{
	protected $search;
	protected $columns = ['username', 'email', 'name'];

	public function __construct($search)
	{
		$this->search = $search;
	}

	public function apply($query)
	{
		$query->where(function($query) {
			$value = '%'.str_replace(' ', '%', $this->search).'%';
			foreach ($this->columns as $column) {
				$query->orWhere($column, 'like', $value);
			}
		});
	}
}
