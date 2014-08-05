<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Users\Traits;

use InvalidArgumentException;

trait UserLevelModelTrait
{
	protected static $accessLevels = [
		'user' => 1,
		'mod' => 10,
		'admin' => 100,
		'superadmin' => 255,
	];

	/**
	 * Get the list of access levels.
	 *
	 * @return array
	 */
	public function getAccessLevels()
	{
		return static::$accessLevels;
	}

	/**
	 * Check if a user has access to a certain level of actions.
	 *
	 * @param  string  $access
	 *
	 * @return boolean
	 */
	public function hasAccess($access)
	{
		if ($access === '*') {
			$access = 'superadmin';
		}

		return $this->user_level >= $this->getUserLevelValue($access);
	}

	/**
	 * Make sure the user_level attribute is cast to an int.
	 *
	 * @param  string $value
	 *
	 * @return int
	 */
	public function getUserLevelAttribute($value)
	{
		return (int) $value;
	}

	/**
	 * Set the user_level attribute.
	 *
	 * @param string|int $value
	 */
	public function setUserLevelAttribute($value)
	{
		$this->attributes['user_level'] = $this->getUserLevelValue($value);
	}

	/**
	 * Getter for user_type.
	 *
	 * @return string
	 */
	public function getUserTypeAttribute()
	{
		$types = array_reverse(static::$accessLevels);
		$level = $this->user_level;

		foreach ($types as $name => $min) {
			if ($level >= $min) {
				return $name;
			}
		}
	}

	/**
	 * Setter for user_type.
	 *
	 * @param string $value
	 */
	public function setUserTypeAttribute($value)
	{
		$this->setUserLevelAttribute($value);
	}

	/**
	 * Get the real user level value from a string or integer. Throws an
	 * exception if the value is invalid.
	 *
	 * @param  mixed $value
	 *
	 * @return int
	 */
	public function getUserLevelValue($value)
	{
		if (is_int($value)) {
			if ($value < 0 || $value > 255) {
				throw new InvalidArgumentException("Invalid access level: $value");
			}
		} else {
			if (!array_key_exists($value, static::$accessLevels)) {
				throw new InvalidArgumentException("Invalid access level: $value");
			}

			$value = static::$accessLevels[$value];
		}

		return $value;
	}
}
