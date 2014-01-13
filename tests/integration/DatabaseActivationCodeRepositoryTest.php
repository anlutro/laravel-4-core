<?php

use c\Auth\Activation\DatabaseActivationCodeRepository;
use Illuminate\Support\Facades\Facade;
use Mockery as m;

class DatabaseActivationCodeRepositoryTest extends SQLiteTestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->app = ['db' => $this->capsule];
		Facade::setFacadeApplication($this->app);
		(new CreateUserActivationTable)->up();
	}

	public function tearDown()
	{
		(new CreateUserActivationTable)->down();
		m::close();
	}

	public function testCreateAndRetrieve()
	{
		$repo = $this->makeRepo();
		$user = $this->makeUser(['email' => 'test@test.com']);

		$this->assertEquals(1, $repo->create($user, 'foo'));

		$email = $repo->retrieveEmailByCode('foo');
		$this->assertEquals('test@test.com', $email);
	}

	public function testDeleteCode()
	{
		$repo = $this->makeRepo();
		$user = $this->makeUser(['email' => 'test@test.com']);
		$repo->create($user, 'foo');
		$repo->delete('foo');
		
		$this->assertNull($repo->retrieveEmailByCode('foo'));
	}

	public function testDeleteUser()
	{
		$repo = $this->makeRepo();
		$user = $this->makeUser(['email' => 'test@test.com']);
		$repo->create($user, 'foo');
		$repo->deleteUser($user);
		
		$this->assertNull($repo->retrieveEmailByCode('foo'));
	}

	protected function makeRepo()
	{
		return new DatabaseActivationCodeRepository($this->app['db']->connection(), 'user_activation');
	}

	public function makeUser(array $attr)
	{
		return new \c\Auth\UserModel($attr);
	}
}
