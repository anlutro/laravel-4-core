<?php
/**
 * Laravel 4 Core - Acrivation code repository utilizing datbaase
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth\Activation;

use Carbon\Carbon;
use Illuminate\Database\Connection;

class DatabaseActivationCodeRepository implements ActivationCodeRepositoryInterface
{
	protected $db;
	protected $table;

	public function __construct(Connection $db, $table)
	{
		$this->db = $db;
		$this->table = $table;
	}

	/**
	 * Create a new activation code row in the database.
	 *
	 * @param  ActivatableInterface $user
	 * @param  string               $code
	 *
	 * @return int  number of inserted rows
	 */
	public function create(ActivatableInterface $user, $code)
	{
		$dt = Carbon::now()->addDay();

		$data = [
			'code' => $code,
			'email' => $user->getActivationEmail(),
			'expires' => $dt,
		];

		return $this->newQuery()->insert($data);
	}

	/**
	 * Retrieve a code from the database.
	 *
	 * @param  string $code
	 *
	 * @return array
	 */
	public function retrieveByCode($code)
	{
		$dt = Carbon::now();

		return $this->findCodeQuery($code)
			->where('expires', '>', $dt)
			->first();
	}

	/**
	 * Delete a code from the database.
	 *
	 * @param  string $code
	 *
	 * @return int  number of affected rows
	 */
	public function delete($code)
	{
		return $this->findCodeQuery($code)->delete();
	}

	/**
	 * Delete all codes belonging to a user.
	 *
	 * @param  ActivatableInterface $user
	 *
	 * @return int  number of affected rows
	 */
	public function deleteUser(ActivatableInterface $user)
	{
		return $this->newQuery()
			->where('email', '=', $user->getActivationEmail())
			->delete();
	}

	/**
	 * Get a new query builder for a certain code.
	 *
	 * @param  string $code
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function findCodeQuery($code)
	{
		return $this->newQuery()
			->where('code', '=', $code);
	}

	/**
	 * Get a new query builder for a certain code.
	 *
	 * @param  string $code
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function newQuery()
	{
		return $this->db->table($this->table);
	}
}
