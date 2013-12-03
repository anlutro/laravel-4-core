<?php

use c\Auth\UserModel as User;
use c\Auth\UserRepository;
use Illuminate\Support\Facades\Facade;
use Mockery as m;

class AuthUserRepositoryTest extends SQLiteTestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->app = [
			'db' => $this->capsule,
			'hash' => new Illuminate\Hashing\BcryptHasher,
		];
		Facade::setFacadeApplication($this->app);
		(new CreateUserTable)->up();
	}

	public function tearDown()
	{
		(new CreateUserTable)->down();
	}

	public function testGetByCredentials()
	{
		$this->createUser('name');
		$repo = $this->makeRepository();

		$this->assertNull($repo->getByCredentials(['username' => 'nonexistant']));
		$this->assertNotNull($repo->getByCredentials(['username' => 'name']));
		$this->assertNotNull($repo->getByCredentials(['username' => 'name', 'password' => 'foo']));
		$this->assertNotNull($repo->getByCredentials(['username' => 'name', 'password' => 'bar']));
	}

	public function testInvalidCreate()
	{
		$repo = $this->makeRepository();
		$this->validator->shouldReceive('validCreate')->once()->andReturn(false);
		$this->assertFalse($repo->create());
	}

	public function testCreateAndActivate()
	{
		$repo = $this->makeRepository();
		$this->validator->shouldReceive('validCreate')->once()->andReturn(true);
		$input = $this->getUserAttributes('foo');
		$user = $repo->create($input, true);
		$this->assertTrue($user->exists, 'User does not exist.');
		$this->assertTrue($user->is_active, 'User is not active.');
	}

	public function testCreateWithoutActivation()
	{
		$this->markTestIncomplete();

		$repo = $this->makeRepository();
		$this->validator->shouldReceive('validCreate')->once()->andReturn(true);
		c\Auth\Activation\Activation::setFacadeApplication(null);
		c\Auth\Activation\Activation::shouldReceive('generate')->once();
		c\Auth\Activation\Activation::setFacadeApplication($this->app);
		$input = $this->getUserAttributes('foo');
		$user = $repo->create($input, false);
		$this->assertFalse($user->is_active, 'User is not active.');
	}

	protected function makeRepository()
	{
		$this->model = new User;
		$this->validator = m::mock('c\Auth\UserValidator');
		return new UserRepository($this->model, $this->validator);
	}

	protected function createUser($name, $password = 'foo', $userLevel = 'user')
	{
		$attr = $this->getUserAttributes($name, $password, $userLevel);
		$user = new User;
		$user->username = $attr['username'];
		$user->name = $attr['name'];
		$user->password = $attr['password'];
		$user->user_type = $attr['user_type'];
		$user->email = $attr['email'];
		$user->phone = $attr['phone'];
		$user->save();
		return $user;
	}

	protected function getUserAttributes($name, $password = 'foo', $userLevel = 'user')
	{
		return [
			'username' => strtolower($name),
			'name' => ucfirst($name),
			'password' => $password,
			'user_type' => $userLevel,
			'email' => 'test@example.com',
			'phone' => '',
		];
	}
}
