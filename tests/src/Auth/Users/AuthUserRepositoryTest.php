<?php
namespace anlutro\Core\Tests\Auth\Users;

use Mockery as m;

class AuthUserRepositoryTest extends \anlutro\LaravelTesting\EloquentTestCase
{
	/**
	 * @var \Mockery\Mock|\Illuminate\Database\Eloquent\Model
	 */
	protected $model;

	/**
	 * @var \Mockery\Mock|\anlutro\LaravelValidation\Validator
	 */
	protected $validator;

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
		$this->container['hash'] = new \Illuminate\Hashing\BcryptHasher;
	}

	public function tearDown()
	{
		parent::tearDown();
		m::close();
	}

	public function testFindByCredentials()
	{
		$this->createUser('name');
		$repo = $this->makeRepository();

		$this->assertNull($repo->findByCredentials(['username' => 'nonexistant']));
		$this->assertNotNull($repo->findByCredentials(['username' => 'name']));
		$this->assertNotNull($repo->findByCredentials(['username' => 'name', 'password' => 'foo']));
		$this->assertNotNull($repo->findByCredentials(['username' => 'name', 'password' => 'bar']));
	}

	public function testInvalidCreate()
	{
		$repo = $this->makeRepository();
		$this->validator->shouldReceive('valid')->once()->with('create', [])->andReturn(false);
		$this->validator->shouldReceive('getErrors')->once()->andReturn(new \Illuminate\Support\MessageBag);
		$this->assertFalse($repo->create([]));
	}

	public function testCreateAsAdminAndActivate()
	{
		$repo = $this->makeRepository();
		$input = $this->getUserAttributes('foo');
		$input['is_active'] = '1';
		$this->validator->shouldReceive('valid')->once()->with('create', $input)->andReturn(true);
		$user = $repo->createAsAdmin($input);
		$this->assertInstanceOf('anlutro\Core\Auth\Users\UserModel', $user);
		$this->assertTrue($user->exists, 'User should exist.');
		$this->assertTrue($user->is_active, 'User should be active.');
	}

	public function testCreateAsAdminWithoutActivation()
	{
		$repo = $this->makeRepository();
		$input = $this->getUserAttributes('foo');
		$this->validator->shouldReceive('valid')->once()->with('create', $input)->andReturn(true);
		$user = $repo->createAsAdmin($input, false);
		$this->assertInstanceOf('anlutro\Core\Auth\Users\UserModel', $user);
		$this->assertFalse($user->is_active, 'User should not be active.');
	}

	public function testCreate()
	{
		$repo = $this->makeRepository();
		$input = $this->getUserAttributes('foo');
		$input['user_level'] = 100;
		$input['is_active'] = true;
		$this->validator->shouldReceive('valid')->once()->with('create', $input)->andReturn(true);
		$user = $repo->create($input, false);
		$this->assertInstanceOf('anlutro\Core\Auth\Users\UserModel', $user);
		$this->assertFalse($user->is_active, 'User should not be active.');
		$this->assertEquals(1, $user->user_level);
	}

	public function testUpdateWithBlankPassword()
	{
		$repo = $this->makeRepository();
		$input = ['name' => 'New Name', 'password' => ''];
		$this->validator->shouldReceive('valid')->once()->with('update', $input)->andReturn(true);

		$user = $this->createUser('name', 'pass');
		$this->validator->shouldReceive('replace')->once()->with('key', $user->getKey());
		$oldpw = $user->password;

		$repo->update($user, $input);

		$this->assertEquals('New Name', $user->name);
		$this->assertEquals($oldpw, $user->password);
	}

	public function testUpdateWithNewPassword()
	{
		$repo = $this->makeRepository();
		$input = ['name' => 'New Name', 'password' => 'newpass'];
		$this->validator->shouldReceive('valid')->once()->with('update', $input)->andReturn(true);

		$user = $this->createUser('name', 'pass');
		$this->validator->shouldReceive('replace')->once()->with('key', $user->getKey());
		$oldpw = $user->password;

		$repo->update($user, $input);

		$this->assertEquals('New Name', $user->name);
		$this->assertNotEquals($oldpw, $user->password);
	}

	public function testUpdateProfileUpdatesCorrectFields()
	{
		$repo = $this->makeRepository();
		$input = ['name' => 'New Name', 'password' => 'newpass', 'user_type' => 'admin', 'username' => 'newname'];
		$this->validator->shouldReceive('valid')->once()->with('update', $input)->andReturn(true);

		$user = $this->createUser('name', 'pass', 'user');
		$this->validator->shouldReceive('replace')->once()->with('key', $user->getKey());
		$oldpw = $user->password;

		$repo->update($user, $input);

		$this->assertEquals('New Name', $user->name);
		$this->assertNotEquals($oldpw, $user->password);
		$this->assertEquals('name', $user->username);
		$this->assertEquals('user', $user->user_type);
	}

	public function testUpdateAsAdmin()
	{
		$repo = $this->makeRepository();
		$input = ['name' => 'New Name', 'password' => 'newpass', 'user_type' => 'mod', 'username' => 'newname'];
		$this->validator->shouldReceive('valid')->once()->with('update', $input)->andReturn(true);
		$user = $this->createUser('name', 'pass', 'user'); $oldpw = $user->password;
		$this->validator->shouldReceive('replace')->once()->with('key', $user->getKey());

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
		$this->createUser('Foo Bar');
		$this->createUser('Bar Baz');

		$users = $repo->search('foo')->getAll();
		$this->assertEquals(1, $users->count());
		$this->assertEquals('Foo Bar', $users->first()->name);

		$users = $repo->search('baz')->getAll();
		$this->assertEquals(1, $users->count());
		$this->assertEquals('Bar Baz', $users->first()->name);
	}

	protected function makeRepository()
	{
		$this->model = new \anlutro\Core\Auth\Users\UserModel;
		$this->validator = m::mock('anlutro\Core\Auth\Users\UserValidator');
		$this->validator->shouldReceive('replace')->with('table', $this->model->getTable());
		return new \anlutro\Core\Auth\Users\UserRepository($this->model, $this->validator);
	}

	protected function createUser($name, $password = 'foo', $userLevel = 'user')
	{
		$attr = $this->getUserAttributes($name, $password, $userLevel);
		$user = new \anlutro\Core\Auth\Users\UserModel;
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
