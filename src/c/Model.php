<?php
namespace c;

class Model extends \Illuminate\Database\Eloquent\Model implements \JsonSerializable
{
	public function jsonSerialize()
	{
		return $this->toArray();
	}
}
