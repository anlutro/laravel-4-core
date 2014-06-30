<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Eloquent;

use Illuminate\Support\Contracts\ArrayableInterface;
use JsonSerializable;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel implements JsonSerializable, StdClassableInterface
{
	/**
	 * Convert the model to an StdClass.
	 *
	 * @return \StdClass
	 */
	public function toStdClass()
	{
		$attributes = $this->getArrayableAttributes();

		foreach ($this->getDates() as $key) {
			if (!isset($attributes[$key])) continue;

			$attributes[$key] = $this->asDateTime($attributes[$key]);
		}

		foreach ($this->getMutatedAttributes() as $key) {
			if (!array_key_exists($key, $attributes)) continue;

			$attributes[$key] = $this->mutateAttributeForArray($key, $attributes[$key]);
		}

		foreach ($this->appends as $key) {
			$attributes[$key] = $this->mutateAttributeForArray($key, null);
		}

		foreach ($this->getArrayableRelations() as $key => $value) {
			if (in_array($key, $this->hidden)) continue;

			if ($value instanceof StdClassableInterface) {
				$attributes[$key] = $value->toStdClass();
			} else if ($value instanceof ArrayableInterface) {
				$attributes[$key] = $value->toArray();
			} else {
				$attributes[$key] = $value;
			}
		}

		return empty($attributes) ? (new \StdClass) : json_decode(json_encode($attributes));
	}

	/**
	 * Is used when json_encode is called on the model.
	 *
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}

	/**
	 * Override the default collection class.
	 *
	 * @param  array  $models
	 *
	 * @return \anlutro\Core\Eloquent\Collection
	 */
	public function newCollection(array $models = array())
	{
		return new Collection($models);
	}
}
