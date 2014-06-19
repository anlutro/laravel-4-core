<?php

class DatabaseActivationCodeRepositoryTest extends \anlutro\LaravelTesting\EloquentTestCase
{
	public function getMigrations()
	{
		return ['UserActivationCreateTable'];
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
		return new anlutro\Core\Auth\Activation\DatabaseActivationCodeRepository(
			$this->capsule->connection(), 'user_activation');
	}

	public function makeUser(array $attr)
	{
		return new anlutro\Core\Auth\Users\UserModel($attr);
	}
}
