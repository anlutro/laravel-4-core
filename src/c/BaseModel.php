<?php
namespace c;

class BaseModel extends \Illuminate\Database\Eloquent\Model implements \JsonSerializable
{
	public function jsonSerialize()
	{
		return $this->toArray();
	}

	public function newCollection(array $models = array())
	{
		return new \c\BaseCollection($models);
	}
}
