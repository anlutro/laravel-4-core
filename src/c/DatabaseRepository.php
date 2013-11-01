<?php
/**
 * Laravel 4 Core - Database repository class
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c;

use Illuminate\Database\Connection;

/**
 * Prototype database repository implementation
 */
abstract class DatabaseRepository
{
	protected $db;

	protected $table;

	protected $paginate = false;

	public function __construct(Connection $db)
	{
		$this->setConnection($db);
	}

	public function setTable($table)
	{
		$this->table = (string) $table;
	}

	public function getTable()
	{
		return $this->table;
	}

	public function setConnection(Connection $db)
	{
		$this->db = $db;
	}

	public function getConnection()
	{
		return $this->db;
	}

	public function getAll()
	{
		$query = $this->newQuery();

		return $this->runQuery($query);
	}

	public function getByKey($key, $keyName = 'id')
	{
		$query = $this->newQuery();

		return $query->where($keyName, '=', $key)
			->first();
	}

	public function update($key, array $attributes, $keyName = 'id')
	{
		$query = $this->newQuery();

		return $query->where($keyName, '=', $key)
			->update($attributes);
	}

	public function updateEntity($entity, $keyName = 'id')
	{
		$attributes = (array) $entity;

		return $this->update($entity->$keyName, $attributes, $keyName);
	}

	public function delete($key, $keyName = 'id')
	{
		$query = $this->newQuery();

		return $query->where($keyName, '=', $key)
			->delete();
	}

	public function deleteEntity($entity, $keyName = 'id')
	{
		return $this->delete($entity->$keyName, $keyName);
	}

	public function create(array $attributes)
	{
		$query = $this->newQuery();

		return $query->insert($attributes);
	}

	public function createEntity($entity)
	{
		return $this->create((array) $entity);
	}

	public function getNew()
	{
		$schema = $this->db->getDoctrineSchemaManager();
		$columns = array_keys($schema->listTableColumns($table));
		$entity = (object) array_fill_keys($columns, null);
		return $entity;
	}

	protected function newQuery()
	{
		return $this->db->table($this->table);
	}

	/**
	 * Toggle pagination. False or no arguments to disable pagination, otherwise
	 * provide a number of items to show per page.
	 *
	 * @param  mixed $paginate
	 *
	 * @return void
	 */
	public function togglePagination($paginate = false)
	{
		if ($paginate === false) {
			$this->paginate = false;
		} else {
			$this->paginate = (int) $paginate;
		}
	}

	/**
	 * Run a query builder and return its results.
	 *
	 * @param  $query  query builder instance/reference
	 *
	 * @return mixed
	 */
	protected function runQuery($query)
	{
		$this->prepareQuery($query);

		if ($this->paginate === false) {
			return $query->get();
		} else {
			return $query->paginate($this->paginate);
		}
	}

	/**
	 * This function is ran by runQuery before fetching the results. Put default
	 * behaviours in this function.
	 *
	 * @param  $query  query builder instance/reference
	 *
	 * @return void
	 */
	protected function prepareQuery($query) {}
}
