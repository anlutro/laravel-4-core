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
	 * Add a subquery select to the builder.
	 *
	 * @param  $query
	 * @param  $subQuery  Can be a closure or a query builder instance.
	 * @param  $as        What to select the subquery as (optional)
	 *
	 * @return [type]           [description]
	 */
	public function scopeAddSubSelect($query, $subQuery, $as = null)
	{
		if ($subQuery instanceof \Closure) {
			$newQuery = $this->newQuery();
			$subQuery($newQuery);
			$subQuery = $newQuery;
		} elseif ($subQuery instanceof EloquentBuilder) {
			$subQuery = $subQuery->getQuery();
		} elseif (!$subQuery instanceof QueryBuilder) {
			throw new \InvalidArgumentException;
		}

		// if there aren't any columns selected already, we need to add the
		// "default" one of table.* - maybe this should be removed.
		if (empty($query->getQuery()->columns)) {
			$query->addSelect($this->table . '.*');			
		}

		// get the SQL from the builder
		$sql = '(' . $subQuery->toSql() . ')';
		if ($as !== null) {
			$sql .= ' as ' . $as;
		}

		// and finally merge it all together
		$query->mergeBindings($subQuery)
			->addSelect( new Expression($sql) );

		return $query;
	}
}
