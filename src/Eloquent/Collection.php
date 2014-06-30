<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Eloquent;

use JsonSerializable;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Contracts\ArrayableInterface;

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
	 * Is used when json_encode is called on the collection.
	 *
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->flatten()->toArray();
	}
}
