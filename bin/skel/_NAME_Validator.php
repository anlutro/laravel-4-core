<?php
namespace _NAMESPACE_;

/**
 * Validator service for _NAME_Repository
 */
class _NAME_Validator extends \c\Validator
{
	public function __construct(_NAME_Model $model)
	{
		parent::__construct($model);
	}

	/**
	 * Common rules are ran on every validation call.
	 */
	protected function getCommonRules()
	{
		return [

		];
	}

	/**
	 * Create rules are only ran when creating a new model.
	 */
	protected function getCreateRules()
	{
		return [
			
		];
	}

	/**
	 * Update rules are only ran when updating an existing model.
	 */
	protected function getUpdateRules()
	{
		return [
			
		];
	}
}
