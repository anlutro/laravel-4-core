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

	public function retrieveByCode($code)
	{
		$dt = Carbon::now();

		return $this->findCodeQuery($code)
			->where('expires', '>', $dt)
			->first();
	}

	public function delete($code)
	{
		return $this->findCodeQuery($code)->delete();
	}

	protected function findCodeQuery($code)
	{
		return $this->newQuery()
			->where('code', '=', $code);
	}

	protected function newQuery()
	{
		return $this->db->table($this->table);
	}
}
