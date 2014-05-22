<?php

use Mockery as m;

class AuthUserRepositoryTest extends \anlutro\LaravelTesting\EloquentTestCase
{
	/**
	 * The migrations the test depends on.
	 */
	protected function getMigrations()
	{
		return ['UsersCreateTable'];
	}

	/**
	 * Call setUpFacades to make the Hash:: facade available throughout the test.
	 */
	public function setUp()
	{
		parent::setUp();
		$this->container['hash'] = new Illuminate\Hashing\BcryptHasher;
	}

	public function tearDown()
	{
		parent::tearDown();
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
		$this->validator->shouldReceive('getErrors')->once()->andReturn(new Illuminate\Support\MessageBag);
		$this->assertFalse($repo->create([]));
	}

	public function testCreateAsAdminAndActivate()
	{
		$repo = $this->makeRepository();
		$this->validator->shouldReceive('validCreate')->once()->andReturn(true);
		$input = $this->getUserAttributes('foo');
		$input['is_active'] = '1';
		$user = $repo->createAsAdmin($input);
		$this->assertInstanceOf('anlutro\Core\Auth\UserModel', $user);
		$this->assertTrue($user->exists, 'User should exist.');
		$this->assertTrue($user->is_active, 'User should be active.');
	}

	public function testCreateAsAdminWithoutActivation()
	{
		$repo = $this->makeRepository();
		$this->validator->shouldReceive('validCreate')->once()->andReturn(true);
		$input = $this->getUserAttributes('foo');
		$user = $repo->createAsAdmin($input, false);
		$this->assertInstanceOf('anlutro\Core\Auth\UserModel', $user);
		$this->assertFalse($user->is_active, 'User should not be active.');
	}

	public function testCreate()
	{
		$repo = $this->makeRepository();
		$this->validator->shouldReceive('validCreate')->once()->andReturn(true);
		$input = $this->getUserAttributes('foo');
		$input['user_level'] = 100;
		$input['is_active'] = true;
		$user = $repo->create($input, false);
		$this->assertInstanceOf('anlutro\Core\Auth\UserModel', $user);
		$this->assertFalse($user->is_active, 'User should not be active.');
		$this->assertEquals(1, $user->user_level);
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

	public function testUpdateAsAdmin()
	{
		$repo = $this->makeRepository();
		$this->validator->shouldReceive('validUpdate')->once()->andReturn(true);
		$user = $this->createUser('name', 'pass', 'user'); $oldpw = $user->password;
		$this->validator->shouldReceive('replace')->once()->with('key', $user->getKey());
		$input = ['name' => 'New Name', 'password' => 'newpass', 'user_type' => 'mod', 'username' => 'newname'];

		$repo->updateAsAdmin($user, $input);

		$this->assertEquals('New Name', $user->name);
		$this->assertNotEquals($oldpw, $user->password);
		$this->assertEquals('newname', $user->username);
		$this->assertEquals('mod', $user->user_type);
	}

	public function testFilter()
	{
		$repo = $this->makeRepository();
		$user = $this->createUser('username', 'pass', 'user');
		$user = $this->createUser('adminname', 'pass', 'admin');

		$users = $repo->filter('user')->getAll();
		$this->assertEquals(1, $users->count());
		$this->assertEquals('Username', $users->first()->name);

		$users = $repo->filter('admin')->getAll();
		$this->assertEquals(1, $users->count());
		$this->assertEquals('Adminname', $users->first()->name);
	}

	public function testSearch()
	{
		$repo = $this->makeRepository();
		$user = $this->createUser('Foo Bar');
		$user = $this->createUser('Bar Baz');

		$users = $repo->search('foo')->getAll();
		$this->assertEquals(1, $users->count());
		$this->assertEquals('Foo Bar', $users->first()->name);

		$users = $repo->search('baz')->getAll();
		$this->assertEquals(1, $users->count());
		$this->assertEquals('Bar Baz', $users->first()->name);
	}

	protected function makeRepository()
	{
		$this->model = new anlutro\Core\Auth\UserModel;
		$this->validator = m::mock('anlutro\Core\Auth\UserValidator');
		$this->validator->shouldReceive('replace')->with('table', $this->model->getTable());
		return new anlutro\Core\Auth\UserRepository($this->model, $this->validator);
	}

	protected function createUser($name, $password = 'foo', $userLevel = 'user')
	{
		$attr = $this->getUserAttributes($name, $password, $userLevel);
		$user = new anlutro\Core\Auth\UserModel;
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
