<?php
namespace c\Auth\Activation;

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

	public function retrieveByCode($code)
	{
		return $this->findCodeQuery($code)->first();
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
