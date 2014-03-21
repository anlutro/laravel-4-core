<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Activation;

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
	 * {@inheritdoc}
	 */
	public function create(ActivatableInterface $user, $code)
	{
		$dt = Carbon::now()->addDay();

		$data = [
			'code' => $code,
			'email' => $user->getActivationEmail(),
			'expires' => $dt,
		];

		return (bool) $this->newQuery()->insert($data);
	}

	/**
	 * {@inheritdoc}
	 */
	public function retrieveEmailByCode($code)
	{
		$dt = Carbon::now();

		$result = $this->findCodeQuery($code)
			->where('expires', '>', $dt)
			->first();

		if ($result === null) return null;

		return $result['email'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete($code)
	{
		return (bool) $this->findCodeQuery($code)->delete();
	}

	/**
	 * {@inheritdoc}
	 */
	public function deleteUser(ActivatableInterface $user)
	{
		return (bool) $this->newQuery()
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
