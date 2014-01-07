<?php
namespace _NAMESPACE_;

/**
 * Repository for _NAME_Model
 */
class _NAME_Repository extends \c\EloquentRepository
{
	public function __construct(_NAME_Model $model, _NAME_Validator $validator)
	{
		parent::__construct($model, $validator);
	}
}
