<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c;

use JsonSerializable;
use Illuminate\Database\Eloquent\Collection;

class BaseCollection extends Collection implements JsonSerializable
{
	/**
	 * Freeze the collection.
	 *
	 * @return void
	 */
	public function freeze()
	{
		$this->each(function($item) {
			if (method_exists($item, 'freeze')) $item->freeze();
		});
	}

	/**
	 * Convert the collection to an array of StdClasses.
	 *
	 * @return StdClass
	 */
	public function toStdClass()
	{
		return json_decode(json_encode($this->toArray()));
	}

	/**
	 * Is used when json_encode is called on the collection.
	 *
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}
}
