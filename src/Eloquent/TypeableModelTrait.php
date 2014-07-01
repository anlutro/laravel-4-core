<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Eloquent;

/**
 * Generic traits for models that store their type as an integer but also needs
 * them to be represented as a string.
 * 
 * Classes that implement this trait must have a protected static $types assoc
 * array of int => string values.
 */
trait TypeableModelTrait
{
	/**
	 * Getter for $model->type. Returns the string representation.
	 */
	public function getTypeAttribute($value)
	{
		if ($value !== null) return $this->getTypeName($value);
	}

	/**
	 * Setter for $model->type
	 */
	public function setTypeAttribute($value)
	{
		$this->attributes['type'] = $this->getTypeValue($value);
	}

	/**
	 * Getter for $model->type_value - returns the raw int value.
	 */
	public function getTypeValueAttribute()
	{
		return $this->getAttributeFromArray('type');
	}

	/**
	 * Get the string representation for a given type.
	 *
	 * @param  string|int $type
	 *
	 * @return string
	 */
	public function getTypeName($type)
	{
		if (is_numeric($type)) {
			if (!isset(static::$types[$type])) {
				throw new \InvalidArgumentException("Unknown type: $type");
			}
			return static::$types[$type];
		} else {
			$type = strtolower($type);
			$types = array_flip(static::$types);
			if (!isset($types[$type])) {
				throw new \InvalidArgumentException("Unknown type: $type");
			}
			return $type;
		}
	}

	/**
	 * Get the int representation for a given type.
	 *
	 * @param  string|int $type
	 *
	 * @return int
	 */
	public function getTypeValue($type)
	{
		if (is_numeric($type)) {
			if (!isset(static::$types[$type])) {
				throw new \InvalidArgumentException("Unknown type: $type");
			}
			return $type;
		} else {
			$type = strtolower($type);
			$types = array_flip(static::$types);
			if (!isset($types[$type])) {
				throw new \InvalidArgumentException("Unknown type: $type");
			}
			return $types[$type];
		}
	}

	/**
	 * Get all available types.
	 *
	 * @return array
	 */
	public function getAvailableTypes()
	{
		return static::$types;
	}

	/**
	 * Add $query->whereType(...) functionality.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder $query
	 * @param  string|int $filter
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeWhereType($query, $filter)
	{
		return $query->where('type', '=', $this->getTypeValue($filter));
	}
}
