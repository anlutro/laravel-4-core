<?php
/**
 * Laravel 4 Core - Base model class
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c;

class BaseModel extends \Illuminate\Database\Eloquent\Model implements \JsonSerializable
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
