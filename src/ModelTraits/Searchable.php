<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\ModelTraits;

/**
 * Trait for models you can search on. Enables $query->search('foo') if $query
 * is derived from a model using this trait.
 */
trait Searchable
{
	public function scopeSearch($query, $searchFor)
	{
		$searchFor = '%'.$searchFor.'%';
		return $query->where(function($query) use ($searchFor) {
			foreach ($this->searchable as $field) {
				$query->orWhere($field, 'like', $searchFor);
			}
		});
	}
}
