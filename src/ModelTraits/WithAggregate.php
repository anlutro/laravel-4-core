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
 * Trait for withAggregate scope functionality.
 */
trait WithAggregate
{
	/**
	 * Lets you eager load an aggregate (sum, average etc.) of a related model
	 * column.
	 * 
	 * The "withAggregate" function call should be placed before any where calls
	 * on the builder. Doing select() in combination with withAggregate probably
	 * does not work at all.
	 * 
	 * Model::withAggregate('relationship', 'sum', 'related_column')
	 *   ->where('model_field', '>', 10)
	 *   ->get();
	 * 
	 * @todo  any way to get rid of the select table.* at the end?
	 *
	 * @param  Builder $query
	 * @param  string $relation
	 * @param  string $aggregate
	 * @param  string $column
	 *
	 * @return Builder
	 */
	public function scopeWithAggregate($query, $relation, $aggregate, $column)
	{
		if (
			$query->getQuery()->columns === null ||
			!in_array($this->table . '.*', $query->getQuery()->columns)
		) {
			$query->addSelect($this->table . '.*');
		}

		// get a new query builder for the related model
		$builder = $this->$relation()
			->getRelated()
			->newQuery();

		// create the expression for selecting the aggregate
		$expression = new Expression( $aggregate . '(' . $column . ')' );

		// get the query builder for the aggregate
		$subQuery = $this->$relation()
			->getRelationCountQuery($builder, $this->newQuery())
			->select($expression)
			->getQuery();

		// get the SQL from the aggregate builder
		$sql = $subQuery->toSql();

		// and finally merge it all together
		$query->mergeBindings($subQuery)
			->addSelect( new Expression("($sql) as {$aggregate}_{$relation}_{$column}") );

		return $query;
	}
}
