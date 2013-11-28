<?php
namespace c;

class BaseModel extends \Illuminate\Database\Eloquent\Model implements \JsonSerializable
{
	public function jsonSerialize()
	{
		return $this->toArray();
	}
}
