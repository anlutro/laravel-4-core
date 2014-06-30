<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Utility class for joining related tables in Eloquent.
 *
 * (new RelationshipQueryJoiner($eloquentQuery))
 *   ->join('relation');
 */
class RelationshipQueryJoiner
{
	/**
	 * The query builder.
	 *
	 * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
	 */
	protected $query;

	/**
	 * The model.
	 *
	 * @var \Illuminate\Database\Eloquent\Model
	 */
	protected $model;

	/**
	 * This array keeps track of currently joined relationships to try and
	 * prevent overlapping joins.
	 *
	 * @var array
	 */
	protected $joined = [];

	public function __construct(Builder $query)
	{
		$this->query = $query;
		$this->model = $query->getModel();
	}

	/**
	 * Join one or multiple relations onto the query.
	 *
	 * @param  string|array $relations Single string or array of strings with
	 * the name of the relation(s) that should be joined onto the query.
	 * @param  string       $type      The join type - left, inner etc
	 *
	 * @return static
	 */
	public function join($relations, $type = 'left')
	{
		foreach ((array) $relations as $relation) {
			if (in_array($relation, $this->joined)) {
				continue;
			}

			if (strpos($relation, '.') !== false) {
				$this->joinNested($relation, $type);
			} else {
				$this->joined[] = $relation;
				$relation = $this->getRelation($this->model, $relation);
				$this->joinRelation($relation, $type);
			}
		}

		$this->checkQuerySelects();

		// @todo resarch when/if group by's are necessary
		// $this->checkQueryGroupBy();

		return $this;
	}

	/**
	 * @param Model  $model
	 * @param string $name
	 *
	 * @return Relation
	 * @throws \InvalidArgumentException
	 */
	protected function getRelation($model, $name)
	{
		if (!method_exists($model, $name)) {
			$class = get_class($model);
			throw new \InvalidArgumentException("$class has no relation $name");
		}

		return $model->$name();
	}

	protected function joinNested($relation, $type)
	{
		$segments = explode('.', $relation);
		$model = $this->model;
		$current = '';

		foreach ($segments as $segment) {
			$current = $current ? "$current.$segment" : $segment;
			$relation = $this->getRelation($model, $segment);

			if (!in_array($current, $this->joined)) {
				$this->joinRelation($relation, $type);
			}

			$model = $relation->getRelated();
		}
	}

	protected function checkQuerySelects()
	{
		$selects = $this->query->getQuery()->columns;
		$tableSelect = $this->model->getTable().'.*';

		if (empty($selects) || !in_array($tableSelect, $selects)) {
			$this->query->addSelect($tableSelect);
		}
	}

	protected function checkQueryGroupBy()
	{
		$groups = $this->query->getQuery()->groups;
		$keyGroup = $this->model->getQualifiedKeyName();

		if (empty($groups) || !in_array($keyGroup, $groups)) {
			$this->query->groupBy($keyGroup);
		}
	}

	protected function joinRelation(Relation $relation, $type)
	{
		if ($relation instanceof Relations\BelongsToMany) {
			$this->joinManyToManyRelation($relation, $type);
		} else if ($relation instanceof Relations\HasOneOrMany) {
			$this->joinHasRelation($relation, $type);
		} else if ($relation instanceof Relations\BelongsTo) {
			$this->joinBelongsToRelation($relation, $type);
		}
	}

	protected function joinHasRelation(Relations\HasOneOrMany $relation, $type)
	{
		$table = $relation->getRelated()->getTable();
		$foreignKey = $relation->getForeignKey();
		$localKey = $relation->getQualifiedParentKeyName();

		$this->query->join($table, $foreignKey, '=', $localKey, $type);
	}

	protected function joinBelongsToRelation(Relations\BelongsTo $relation, $type)
	{
		$table = $relation->getRelated()->getTable();
		$foreignKey = $relation->getQualifiedForeignKey();
		$localKey = $relation->getQualifiedOtherKeyName();

		$this->query->join($table, $foreignKey, '=', $localKey, $type);
	}

	protected function joinManyToManyRelation(Relations\BelongsToMany $relation, $type)
	{
		$pivotTable = $relation->getTable();
		// $relation->getQualifiedParentKeyName() is protected
		$parentKey = $relation->getParent()->getQualifiedKeyName();
		$localKey = $relation->getOtherKey();

		$this->query->join($pivotTable, $localKey, '=', $parentKey, $type);

		$related = $relation->getRelated();
		$foreignKey = $relation->getForeignKey();
		$relatedTable = $related->getTable();
		$relatedKey = $related->getQualifiedKeyName();

		$this->query->join($relatedTable, $foreignKey, '=', $relatedKey, $type);
	}
}
