<?php
use Mockery as m;

use c\Auth\Activation\DatabaseActivationCodeRepository;

class ActivationCodeRepositoryTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->db = m::mock('Illuminate\Database\Connection');
		$this->repo = new DatabaseActivationCodeRepository($this->db, 'activations');
	}

	public function tearDown()
	{
		m::close();
	}

	public function testCreate()
	{
		// query expectation has to be defined first for some reason
		$this->query()->shouldReceive('insert')->with(m::on(function($data) {
			return ($data['code'] == 'foo') && ($data['email'] == 'test@example.com')
				&& ($data['expires'] instanceof Carbon\Carbon);
		}))->andReturn(true);
		
		$user = m::mock('c\Auth\Activation\ActivatableInterface');
		$user->shouldReceive('getActivationEmail')->once()->andReturn('test@example.com');

		$this->assertTrue($this->repo->create($user, 'foo'));
	}

	public function testRetrieveByCode()
	{
		$this->queryWhere('foo')->shouldReceive('where')->once()
			->with('expires', '>', m::type('Carbon\Carbon'))
			->andReturn(m::self())->getMock()
			->shouldReceive('first')->once()
			->andReturn('bar');
		$result = $this->repo->retrieveByCode('foo');

		$this->assertEquals('bar', $result);
	}

	public function testDelete()
	{
		$query = $this->queryWhere('foo')->shouldReceive('delete')->once()->andReturn(true);
		$result = $this->repo->delete('foo');

		$this->assertTrue($result);
	}

	protected function queryWhere($where)
	{
		return $this->query()->shouldReceive('where')->with('code', '=', $where)
			->andReturn(m::self())->getMock();
	}

	protected function query()
	{
		return $this->db->shouldReceive('table')->with('activations')
			->andReturn(m::self())->getMock();
	}
}
