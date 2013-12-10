<?php
/**
 * Laravel 4 Core - Eloquent repository class
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c;

use Illuminate\Database\Eloquent\Model;

/**
 * Abstract repository that provides some basic functionality.
 */
abstract class EloquentRepository
{
	/**
	 * @var Illuminate\Database\Eloquent\Model
	 */
	protected $model;

	/**
	 * @var c\Validator
	 */
	protected $validator;

	/**
	 * How the repository should paginate.
	 *
	 * @var false|int
	 */
	protected $paginate = false;

	/**
	 * Whether to throw exceptions or return null on single row queries.
	 *
	 * @var boolean
	 */
	protected $throwExceptions = false;

	/**
	 * Dependency inject the model.
	 *
	 * @param Illuminate\Database\Eloquent\Model $model
	 */
	public function __construct(Model $model, Validator $validator)
	{
		$this->model = $model;
		$this->validator = $validator;
	}

	/**
	 * Toggle pagination. False or no arguments to disable pagination, otherwise
	 * provide a number of items to show per page.
	 *
	 * @param  mixed $paginate
	 *
	 * @return void
	 */
	public function paginate($paginate = false)
	{
		if ($paginate === false) {
			$this->paginate = false;
		} else {
			$this->paginate = (int) $paginate;
		}

		return $this;
	}

	/**
	 * Toggle whether or not to throw exceptions on single row queries.
	 *
	 * @param  boolean $toggle
	 *
	 * @return void
	 */
	public function toggleExceptions($toggle = true)
	{
		$this->throwExceptions = (bool) $toggle;

		return $this;
	}

	/**
	 * Get the repository's model.
	 *
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function getModel()
	{
		return $this->model;
	}

	/**
	 * Set the repository's model.
	 *
	 * @param Illuminate\Database\Eloquent\Model $model
	 */
	public function setModel(Model $model)
	{
		$this->model = $model;

		return $this;
	}

	/**
	 * Get all the rows from the database.
	 *
	 * @return mixed
	 */
	public function getAll()
	{
		$query = $this->model->newQuery();

		return $this->fetchMany($query);
	}

	/**
	 * Get a single row by its primary key.
	 *
	 * @param  mixed $key
	 *
	 * @return Illuminate\Database\Eloquent\Model|null
	 */
	public function getByKey($key)
	{
		$query = $this->model->newQuery()
			->where($this->model->getKeyName(), $key);

		return $this->fetchSingle($query);
	}

	/**
	 * Save changes an existing model instance.
	 *
	 * @param  Illuminate\Database\Eloquent\Model $model
	 * @param  array $attributes
	 *
	 * @return boolean
	 */
	public function update(Model $model, array $attributes)
	{
		if (!$model->exists) {
			throw new \RuntimeException('Cannot update non-existing model');
		}

		$this->validator->setKey($model->getKey());
		
		if (!$this->validator->validUpdate($attributes)) {
			return false;
		}

		$model->fill($attributes);

		return $model->save();
	}

	/**
	 * Delete an existing model instance.
	 *
	 * @param  Model  $model
	 *
	 * @return boolean
	 */
	public function delete(Model $model)
	{
		return $model->delete();
	}

	/**
	 * Create and validate a new model instance without saving it.
	 *
	 * @param  array  $attributes
	 *
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function getNew(array $attributes = array())
	{
		if (!$this->validator->validCreate($attributes)) {
			return false;
		}

		return $this->model->newInstance($attributes);
	}

	/**
	 * Get a new model instance.
	 *
	 * @param  array  $attributes
	 *
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function newInstance(array $attributes = array())
	{
		return $this->model->newInstance($attributes);
	}

	/**
	 * Create a new model instance and save it to the database.
	 *
	 * @param  array  $attributes optional
	 *
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function create(array $attributes = array())
	{
		$model = $this->getNew($attributes);

		if (!$model) {
			return false;
		}

		$model->save();
		return $model;
	}

	/**
	 * Run a query builder and return a collection of rows.
	 *
	 * @param  $query  query builder instance/reference
	 *
	 * @return mixed
	 */
	protected function fetchMany($query)
	{
		$this->prepareQuery($query);

		if ($this->paginate === false) {
			return $query->get();
		} else {
			return $query->paginate($this->paginate);
		}
	}

	/**
	 * Run a query builder and return a single row.
	 *
	 * @param  $query  query builder
	 *
	 * @return mixed
	 */
	protected function fetchSingle($query)
	{
		$this->prepareQuery($query);

		if ($this->throwExceptions === true) {
			return $query->firstOrFail();
		}

		return $query->first();
	}

	/**
	 * This method is called before fetchMany and fetchSingle. Use it to add
	 * functionality that should be present on every query.
	 *
	 * @param  $query  query builder instance/reference
	 *
	 * @return void
	 */
	protected function prepareQuery($query) {}

	/**
	 * Get the validation errors.
	 */
	public function errors()
	{
		return $this->validator->errors();
	}
}
