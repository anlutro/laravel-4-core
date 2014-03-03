<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c\ModelTraits;

use Illuminate\Database\Query\Expression;

/**
 * Trait for withConstrainedRelationCount scope functionality.
 */
trait WithConstrainedRelationCount
{
	/**
	 * Allows for eager loading of relation counts with constraints.
	 * 
	 * Model::withConstrainedRelationCount(['relation' => function($query) {
	 *     $query->where('field', '=', 'value');
	 * }])->get();
	 * 
	 * Returns the model plus a field for count of relations where field=value.
	 * 
	 * $model->relation_count;
	 * 
	 * @todo  any way to get rid of the select table.* at the end?
	 *
	 * @param  Builder $query
	 * @param  array   $relations  ['rel' => function($query) { ... }]
	 *
	 * @return Builder
	 */
	public function scopeWithConstrainedRelationCount($query, array $relations)
	{
		$relations = (array) $relations;

		if (
			$query->getQuery()->columns === null ||
			!in_array($this->table . '.*', $query->getQuery()->columns)
		) {
			$query->addSelect($this->table . '.*');
		}

		foreach ($relations as $relation => $constraint) {
			$instance = $this->$relation();

			$relQuery = $instance->getRelated()
				->newQuery();

			$relCountQuery = $instance->getRelationCountQuery($relQuery, $this->newQuery());
			
			$constraint($relCountQuery);

			$subQuery = $relCountQuery->getQuery();

			$sql = $subQuery->toSql();

			$query->addSelect(new Expression("($sql) as {$relation}_count"));

			// mergeBindings doesn't work properly, will combine in the wrong order
			$bindings = array_merge($subQuery->getBindings(), $query->getQuery()->getBindings());
			$query->setBindings($bindings);
		}

		return $query;
	}
}