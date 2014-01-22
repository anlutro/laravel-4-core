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
		m::close();
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
		$this->validator->shouldReceive('errors->all')->once()->andReturn([]);
		$this->assertFalse($repo->create());
	}

	public function testCreateAndActivate()
	{
		$repo = $this->makeRepository();
		$this->validator->shouldReceive('validCreate')->once()->andReturn(true);
		$input = $this->getUserAttributes('foo');
		$input['is_active'] = '1';
		$user = $repo->create($input);
		$this->assertInstanceOf('c\Auth\UserModel', $user);
		$this->assertTrue($user->exists, 'User should exist.');
		$this->assertTrue($user->is_active, 'User should be active.');
	}

	public function testCreateWithoutActivation()
	{
		$repo = $this->makeRepository();
		$this->validator->shouldReceive('validCreate')->once()->andReturn(true);
		$input = $this->getUserAttributes('foo');
		$user = $repo->create($input, false);
		$this->assertInstanceOf('c\Auth\UserModel', $user);
		$this->assertFalse($user->is_active, 'User should not be active.');
	}

	public function testUpdateWithBlankPassword()
	{
		$repo = $this->makeRepository();
		$this->validator->shouldReceive('validUpdate')->once()->andReturn(true);

		$user = $this->createUser('name', 'pass');
		$this->validator->shouldReceive('replace')->once()->with('key', $user->getKey());
		$oldpw = $user->password;
		$input = ['name' => 'New Name', 'password' => ''];

		$repo->update($user, $input);

		$this->assertEquals('New Name', $user->name);
		$this->assertEquals($oldpw, $user->password);
	}

	public function testUpdateWithNewPassword()
	{
		$repo = $this->makeRepository();
		$this->validator->shouldReceive('validUpdate')->once()->andReturn(true);

		$user = $this->createUser('name', 'pass');
		$this->validator->shouldReceive('replace')->once()->with('key', $user->getKey());
		$oldpw = $user->password;
		$input = ['name' => 'New Name', 'password' => 'newpass'];

		$repo->update($user, $input);

		$this->assertEquals('New Name', $user->name);
		$this->assertNotEquals($oldpw, $user->password);
	}

	public function testUpdateProfileUpdatesCorrectFields()
	{
		$repo = $this->makeRepository();
		$this->validator->shouldReceive('validUpdate')->once()->andReturn(true);

		$user = $this->createUser('name', 'pass', 'user');
		$this->validator->shouldReceive('replace')->once()->with('key', $user->getKey());
		$oldpw = $user->password;
		$input = ['name' => 'New Name', 'password' => 'newpass', 'user_type' => 'admin', 'username' => 'newname'];

		$repo->update($user, $input);

		$this->assertEquals('New Name', $user->name);
		$this->assertNotEquals($oldpw, $user->password);
		$this->assertEquals('name', $user->username);
		$this->assertEquals('user', $user->user_type);
	}

	protected function makeRepository()
	{
		$this->model = new User;
		$this->validator = m::mock('c\Auth\UserValidator');
		$this->validator->shouldReceive('replace')->with('table', $this->model->getTable());
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
