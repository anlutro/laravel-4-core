<?php
namespace _NAMESPACE_;

/**
 * Eloquent\database model.
 */
class _NAME_Model extends \c\BaseModel
{
	public function newEloquentBuilder($query)
	{
		return new _NAME_QueryBuilder($query);
	}
}
