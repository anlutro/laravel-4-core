<?php
/**
 * Laravel 4 Core - Eager load relation count with constraint model trait
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
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

		foreach ($relations as $relation => $constraint) {
			$instance = $this->$relation();

			$relQuery = $instance->getRelated()
				->newQuery();

			$relCountQuery = $instance->getRelationCountQuery($relQuery);
			
			$constraint($relCountQuery);

			$subQuery = $relCountQuery->getQuery();

			$sql = $subQuery->toSql();

			$query->mergeBindings($subQuery)
				->addSelect(new Expression("($sql) as {$relation}_count"));
		}

		// dirty hack... if anyone knows how to avoid it please let me know!
		return $query->addSelect($this->table.'.*');
	}
}
