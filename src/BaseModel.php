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
	 * Whether or not the model is frozen.
	 *
	 * @see freeze()
	 *
	 * @var boolean
	 */
	protected $frozen = false;

	/**
	 * Freeze the model, preventing it from writing to the database.
	 *
	 * @return [type] [description]
	 */
	public function freeze()
	{
		$this->frozen = true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save(array $options = array())
	{
		if ($this->frozen) throw new \RuntimeException('Model is frozen.');

		return parent::save();
	}

	/**
	 * Convert the model to an StdClass.
	 *
	 * @return StdClass
	 */
	public function toStdClass()
	{
		$array = $this->toArray();
		return empty($array) ? (new StdClass) : json_decode(json_encode($array));
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
	 * @return \c\BaseCollection
	 */
	public function newCollection(array $models = array())
	{
		return new \c\BaseCollection($models);
	}
}
