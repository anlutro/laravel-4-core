<?php
/**
 * Laravel 4 Core - Validator service
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator as VFactory;

/**
 * Validator class that can be injected into a repository or controller or
 * whatever else for easy validation of Eloquent models.
 */
abstract class Validator
{
	protected $commonRules = [];
	protected $key = 'null';
	protected $validator;

	/**
	 * @param \Illuminate\Database\Eloquent\Model $model
	 */
	public function __construct(Model $model)
	{
		$this->model = $model;
	}

	/**
	 * Set the key of the active model. Should be done before updating if there
	 * are any exists/unique rules.
	 *
	 * @param mixed $key
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}

	public function validCreate(array $attributes)
	{
		return $this->valid([], $attributes);
	}

	public function validUpdate(array $attributes)
	{
		return $this->valid([], $attributes);
	}

	/**
	 * Prepare rules, make the validator and check if it passes.
	 *
	 * @param  array  $rules      [description]
	 * @param  array  $attributes [description]
	 *
	 * @return boolean
	 */
	protected function valid($rules, $attributes)
	{
		$rules = $this->prepareRules($rules);
		$this->validator = VFactory::make($attributes, $rules);
		return $this->validator->passes();
	}

	/**
	 * Prepare the rules - merging with common rules and replacing keys and
	 * table with the model's key and table
	 *
	 * @param  array  $rules
	 *
	 * @return array
	 */
	protected function prepareRules(array $rules)
	{
		$rules = $rules + $this->commonRules;

		array_walk_recursive($rules, function(&$item, $key) {
			if (strpos($item, '<key>') !== false) {
				$item = str_replace('<key>', $this->key, $item);
			}
			if (strpos($item, '<table>') !== false) {
				$item = str_replace('<table>', $this->model->getTable(), $item);
			}
		});

		return $rules;
	}

	/**
	 * Missing method calls to this class will be passed on to the underlying
	 * validator class for convenience.
	 */
	public function __call($method, $args)
	{
		if ($this->validator !== null)
			return call_user_func_array([$this->validator, $method], $args);
		else
			throw new \BadMethodCallException;
	}
}
