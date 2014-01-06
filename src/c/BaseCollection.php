<?php
/**
 * Laravel 4 Core - Base model class
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c;

class BaseCollection extends \Illuminate\Database\Eloquent\Collection implements \JsonSerializable
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
