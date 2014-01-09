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
	protected $commonRules = [
	];

	/**
	 * Create rules are only ran when creating a new model.
	 */
	protected $createRules = [
	];

	/**
	 * Update rules are only ran when updating an existing model.
	 */
	protected $updateRules = [
	];
}
