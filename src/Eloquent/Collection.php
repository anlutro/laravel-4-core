<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Eloquent;

use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Contracts\ArrayableInterface;
use JsonSerializable;

class Collection extends BaseCollection implements JsonSerializable, StdClassableInterface
{
	/**
	 * Convert the collection to an array of StdClass objects.
	 *
	 * @return array
	 */
	public function toStdClass()
	{
		return array_map(function($value) {
			$array = null;

			if (is_object($value)) {
				if ($value instanceof StdClassableInterface) {
					return $value->toStdClass();
				}

				if ($value instanceof ArrayableInterface) {
					$array = $value->toArray();
				} else if ($value instanceof JsonSerializable) {
					$array = $value->jsonSerialize();
				} else {
					$array = (array) $value;
				}
			}

			if (is_array($array)) {
				return json_decode(json_encode($value));
			}

			return $value;
		}, array_values($this->items));
	}

	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize()
	{
		return array_map(function($value)
		{
			if ($value instanceof JsonSerializable) {
				return $value->jsonSerialize();
			} else if ($value instanceof ArrayableInterface) {
				return $value->toArray();
			} else {
				return $value;
			}
		}, array_values($this->items));
	}

	/**
	 * {@inheritdoc}
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->jsonSerialize(), $options);
	}
}
