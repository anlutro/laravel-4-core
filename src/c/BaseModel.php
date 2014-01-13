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
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model implements JsonSerializable
{
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
	 * @return \c\BaseCollection
	 */
	public function newCollection(array $models = array())
	{
		return new \c\BaseCollection($models);
	}
}
