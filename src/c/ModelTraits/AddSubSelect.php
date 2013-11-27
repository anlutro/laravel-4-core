<?php
/**
 * Laravel 4 Core - Add subquery select
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\ModelTraits;

use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Trait for addSubSelect scope functionality.
 */
trait AddSubSelect
{
	/**
	 * Add subquery selects to the builder.
	 * 
	 * Because of the way the query builder stores bindings, you can only call
	 * this method once - calling it more than once will screw up the order
	 * of parameters being passed to the SQL query.
	 *
	 * @param  Builder $query
	 * @param  array   $subQuery  Associative array of alias => closure/query builder
	 *
	 * @return Builder
	 */
	public function scopeAddSubSelects($query, array $subQueries)
	{
		// if there aren't any columns selected already, we need to add the
		// "default" one of table.* - maybe this should be removed.
		if (empty($query->getQuery()->columns) || !in_array($this->table . '.*', $query->getQuery()->columns)) {
			$query->addSelect($this->table . '.*');			
		}

		$bindings = [];

		foreach ($subQueries as $alias => $subQuery) {
			if ($subQuery instanceof \Closure) {
				$newQuery = $this->newQuery();
				$subQuery($newQuery);
				$subQuery = $newQuery;
			} elseif ($subQuery instanceof EloquentBuilder) {
				$subQuery = $subQuery->getQuery();
			} elseif (!$subQuery instanceof QueryBuilder) {
				throw new \InvalidArgumentException;
			}

			// get the SQL from the builder
			$sql = '(' . $subQuery->toSql() . ') as ' . $alias;

			$query->addSelect( new Expression($sql) );
			$bindings = array_merge($bindings, $subQuery->getBindings());
		}

		// mergeBindings doesn't work properly, will combine in the wrong order
		$query->setBindings(array_merge($bindings, $query->getQuery()->getBindings()));

		return $query;
	}
}
