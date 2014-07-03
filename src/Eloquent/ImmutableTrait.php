<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Eloquent;

trait ImmutableTrait
{
	protected $immutable = true;

	public function makeImmutable()
	{
		$this->immutable = true;
	}

	public function makeMutable()
	{
		$this->immutable = false;
	}

	public function save(array $options = array())
	{
		if ($this->immutable) {
			throw new \RuntimeException('Cannot save immutable model');
		}

		return parent::save($options);
	}

	public function setRawAttributes(array $attributes, $sync = false)
	{
		if ($this->immutable) {
			throw new \RuntimeException('Cannot set attributes on immutable model');
		}

		return parent::setRawAttributes($attributes, $sync);
	}

	public function setAttribute($key, $value)
	{
		if ($this->immutable) {
			throw new \RuntimeException('Cannot set attributes on immutable model');
		}

		return parent::setAttribute($key, $value);
	}
}
