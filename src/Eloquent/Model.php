<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Eloquent;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Contracts\ArrayableInterface;
use JsonSerializable;

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
		$append = [];

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
				$append[$key] = $value->toStdClass();
			} else if ($value instanceof ArrayableInterface) {
				$attributes[$key] = $value->toArray();
			} else {
				$attributes[$key] = $value;
			}
		}

		$object = empty($attributes) ? (new \StdClass) : json_decode(json_encode($attributes));

		foreach ($this->getDates() as $key) {
			if (isset($attributes[$key])) {
				$object->$key = $this->asDateTime($attributes[$key]);
			}
		}

		foreach ($append as $key => $value) {
			$object->$key = $value;
		}

		return $object;
	}

	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}

	/**
	 * {@inheritdoc}
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->jsonSerialize(), $options);
	}

	/**
	 * {@inheritdoc}
	 * @return \anlutro\Core\Eloquent\Collection
	 */
	public function newCollection(array $models = array())
	{
		return new Collection($models);
	}
}
