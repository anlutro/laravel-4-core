<?php
namespace c;

class BaseCollection extends \Illuminate\Database\Eloquent\Collection implements \JsonSerializable
{
	public function jsonSerialize()
	{
		return $this->toArray();
	}
}