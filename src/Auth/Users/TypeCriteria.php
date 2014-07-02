<?php
namespace anlutro\Core\Auth\Users;

use anlutro\LaravelRepository\CriteriaInterface;

class TypeCriteria implements CriteriaInterface
{
	const TYPE_EXACT = '=';
	const TYPE_GT = '>=';

	protected $level;
	protected $operator;

	public function __construct($level, $type = TypeCriteria::TYPE_EXACT)
	{
		$this->level = $level;

		if ($type !== static::TYPE_GT && $type !== static::TYPE_EXACT) {
			throw new \InvalidArgumentException('$type must be TYPE_EXACT or TYPE_GT');
		}

		$this->operator = $type;
	}

	public function apply($query)
	{
		$value = $query->getModel()->getUserLevelValue($this->level);

		$query->where('user_level', '=', $value);
	}
}
