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
	 * Is used when json_encode is called on the collection.
	 *
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}
}
