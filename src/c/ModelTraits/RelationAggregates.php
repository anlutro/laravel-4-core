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
 * Trait for relation aggregate functionality. Depends on the AddSubSelect trait.
 */
trait RelationAggregates
{
	/**
	 * Load the count of a relation onto an existing model.
	 *
	 * @param  string   $relation
	 * @param  callbale $constraint optional
	 *
	 * @return void
	 */
	public function loadRelationCount($relation, callable $constraint = null)
	{
		$query = $this->$relation();

		if ($constraint !== null) {
			$constraint($query);
		}

		$this->attributes[$relation . '_count'] = $query->count();
	}

	/**
	 * Load the aggregate of a relation onto an existing model.
	 *
	 * @param  string   $relation
	 * @param  string   $aggregate
	 * @param  string   $column      optional, default '*'
	 * @param  callable $constraint  optional
	 *
	 * @return void
	 */
	public function loadRelationAggregate($relation, $aggregate, $column = '*', callable $constraint = null)
	{
		$query = $this->$relation();

		if ($constraint !== null) {
			$constraint($query);
		}

		$alias = $relation . '_' . $aggregate;
		if ($column !== '*') {
			$alias .= '_' . $column;
		}

		$this->attributes[$alias] = $query->$aggregate($column);
	}

	/**
	 * Allows for eager loading of relation counts.
	 * 
	 * Model::withRelationCount(['relation1', 'relation2'])->get();
	 * 
	 * $model->relation1_count;
	 * $model->relation2_count;
	 * 
	 * @param  Builder $query
	 * @param  array   $relations
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function scopeWithRelationCount($query, $relations)
	{
		$queries = [];

		foreach ((array) $relations as $relation => $constraint) {
			if (is_numeric($relation)) {
				$relation = $constraint;
				$constraint = null;
			}
			$queries[$relation . '_count'] = $this->relationCountQuery($relation, $constraint);
		}

		return $query->addSubSelects($queries);
	}

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
	 * @param  Builder $query
	 * @param  array   $relations  ['rel' => function($query) { ... }]
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function scopeWithConstrainedRelationCount($query, array $relations)
	{
		$queries = [];

		foreach ($relations as $relation => $constraint) {
			$queries[$relation . '_count'] = $this->relationCountQuery($relation, $constraint);
		}

		return $query->addSubSelects($queries);
	}

	/**
	 * Eager load relation aggregates.
	 * 
	 * Model::withRelationAggregate(['relation' => ['sum', 'column']])
	 *
	 * @param  Builder $query
	 * @param  array   $relations
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	public function scopeWithRelationAggregate($query, array $relations)
	{
		$queries = [];

		foreach ($relations as $relation => $aggregate) {
			if (is_array($aggregate)) {
				$column = isset($aggregate[1]) ? $aggregate[1] : null;
				$constraint = isset($aggregate[2]) ? $aggregate[2] : null;
				$alias = $relation . '_' . $aggregate[0];
				if ($column) {
					$alias .= '_' . $column;
				}
				$queries[$alias] = $this->relationAggregateQuery($relation, $aggregate[0], $column, $constraint);
			} else {
				$alias = $relation . '_' . $aggregate;
				$queries[$alias] = $this->relationAggregateQuery($relation, $aggregate);
			}
		}

		return $query->addSubSelects($queries);
	}

	/**
	 * Make a new relation count query.
	 *
	 * @param  string   $relation
	 * @param  callable $constraint optional
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function relationCountQuery($relation, callable $constraint = null)
	{
		$instance = $this->$relation();

		$relQuery = $instance->getRelated()->newQuery();

		$relCountQuery = $instance->getRelationCountQuery($relQuery, $this->newQuery());
		
		if ($constraint !== null) {
			$constraint($relCountQuery);
		}

		return $relCountQuery->getQuery();
	}

	/**
	 * Make a new relation aggregate query.
	 *
	 * @param  string   $relation
	 * @param  string   $aggregate
	 * @param  string   $column     optional, defaults to '*'
	 * @param  callable $constraint optional
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function relationAggregateQuery($relation, $aggregate, $column = '*', callable $constraint = null)
	{
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

		if ($constraint !== null) {
			$constraint($subQuery);
		}

		return $subQuery;
	}
}
