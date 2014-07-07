<?php
namespace anlutro\Core\Tests\Auth;

use Mockery as m;
use PHPUnit_Framework_TestCase;

/** @small */
class AuthUserManagerTest extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		m::close();
	}

	public function testCreateWithSufficientAccess()
	{
		$db = $this->mockDb();
		$users = $this->mockUsers();
		$auth = $this->mockAuth();
		$mng = $this->makeManager($db, $users, $auth, $this->mockTranslator());
		$user = $this->mockUser(); $user->user_level = 100;
		$this->pretendLogin($auth, $user);

		$input = ['username' => 'foobar', 'user_level' => 1];
		$users->shouldReceive('createAsAdmin')->once()->with($input)
			->andReturn($mockUser = $this->mockUser());

		$result = $mng->create($input);

		$this->assertSame($mockUser, $result);
	}

	/**
	 * @expectedException anlutro\Core\Auth\AccessDeniedException
	 */
	public function testCreateWithInsufficientAccess()
	{
		$db = $this->mockDb();
		$users = $this->mockUsers();
		$auth = $this->mockAuth();
		$mng = $this->makeManager($db, $users, $auth, $this->mockTranslator());
		$this->pretendLogin($auth, $user = $this->mockUser());
		$user->access_level = 50;

		$input = ['username' => 'foobar', 'user_level' => 100];

		$result = $mng->create($input);
	}

	public function testCreateAndSendActivation()
	{
		$db = $this->mockDb();
		$users = $this->mockUsers();
		$auth = $this->mockAuth();
		$activations = $this->mockActivations();
		$mng = $this->makeManager($db, $users, $auth, $this->mockTranslator(), $activations);
		$user = $this->mockUser(); $user->user_level = 100;
		$this->pretendLogin($auth, $user);

		$input = ['username' => 'foobar', 'user_level' => '1', 'send_activation' => '1'];
		$users->shouldReceive('createAsAdmin')->once()->with($input)
			->andReturn($mockUser = $this->mockUser());
		$mockUser->is_active = false;
		$activations->shouldReceive('generate')->once()->with($mockUser);

		$this->assertSame($mockUser, $mng->create($input));
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testCreateAndSendActivationWithoutSettingService()
	{
		$db = $this->mockDb();
		$users = $this->mockUsers();
		$auth = $this->mockAuth();
		$mng = $this->makeManager($db, $users, $auth, $this->mockTranslator());
		$user = $this->mockUser(); $user->user_level = 100;
		$this->pretendLogin($auth, $user);

		$input = ['username' => 'foobar', 'user_level' => '1', 'send_activation' => '1'];
		$users->shouldReceive('createAsAdmin')->once()->with($input)
			->andReturn($mockUser = $this->mockUser());
		$mockUser->is_active = false;

		$mng->create($input);
	}

	public function testRegister()
	{
		$db = $this->mockDb();
		$users = $this->mockUsers();
		$auth = $this->mockAuth();
		$activations = $this->mockActivations();
		$mng = $this->makeManager($db, $users, $auth, $this->mockTranslator(), $activations);

		$input = ['username' => 'foobar', 'user_level' => '20', 'user_type' => 'asdf', 'is_active' => '1'];
		$users->shouldReceive('create')->once()->with($input)
			->andReturn($mockUser = $this->mockUser());
		$activations->shouldReceive('generate')->once()->with($mockUser);

		$this->assertSame($mockUser, $mng->register($input));
	}

	public function testUpdateCurrentProfileSuccess()
	{
		$db = $this->mockDb();
		$users = $this->mockUsers(); $auth = $this->mockAuth();
		$mng = $this->makeManager($db, $users, $auth, $this->mockTranslator());
		$input = ['foo' => 'bar', 'old_password' => 'baz'];
		$this->pretendLogin($auth, $mockUser = $this->mockUser());
		$mockUser->shouldReceive('confirmPassword')->once()->with('baz')->andReturn(true);
		$users->shouldReceive('update')->once()->with($mockUser, $input)->andReturn(true);
		$this->assertTrue($mng->updateCurrentProfile($input));
	}

	public function testUpdateCurrentProfileIncorrectPassword()
	{
		$db = $this->mockDb();
		$users = $this->mockUsers(); $auth = $this->mockAuth();
		$mng = $this->makeManager($db, $users, $auth, $this->mockTranslator());
		$input = ['foo' => 'bar', 'old_password' => 'baz'];
		$this->pretendLogin($auth, $mockUser = $this->mockUser());
		$mockUser->shouldReceive('confirmPassword')->once()->with('baz')->andReturn(false);

		$this->setExpectedException('anlutro\LaravelValidation\ValidationException');
		$mng->updateCurrentProfile($input);
	}

	public function testUpdateCurrentProfileValidationFails()
	{
		$db = $this->mockDb();
		$users = $this->mockUsers(); $auth = $this->mockAuth();
		$mng = $this->makeManager($db, $users, $auth, $this->mockTranslator());
		$input = ['foo' => 'bar', 'old_password' => 'baz'];
		$this->pretendLogin($auth, $mockUser = $this->mockUser());
		$mockUser->shouldReceive('confirmPassword')->once()->with('baz')->andReturn(true);
		$users->shouldReceive('update')->once()->with($mockUser, $input)->andReturn(false);
		$this->assertFalse($mng->updateCurrentProfile($input));
	}

	public function testLogin()
	{
		$db = $this->mockDb();
		$users = $this->mockUsers(); $auth = $this->mockAuth();
		$mng = $this->makeManager($db, $users, $auth, $this->mockTranslator());
		$input = ['username' => 'foo', 'password' => 'bar'];
		$auth->shouldReceive('attempt')->once()->with($input + ['is_active' => 1], false)->andReturn(true);
		$auth->shouldReceive('user->rehashPassword')->once()->with('bar');
		$mng->login($input);
	}

	public function testRequestPasswordResetForEmail()
	{
		$db = $this->mockDb();
		$users = $this->mockUsers(); $auth = $this->mockAuth();
		$reminders = $this->mockReminders();
		$mng = $this->makeManager($db, $users, $auth, $this->mockTranslator(), null, $reminders);
		$users->shouldReceive('findByCredentials')->once()->with(['email' => 'foo@bar.com'])->andReturn($mockUser = $this->mockUser());
		$reminders->shouldReceive('requestReset')->once()->with($mockUser)->andReturn(true);
		$this->assertTrue($mng->requestPasswordResetForEmail('foo@bar.com'));
	}

	public function testResetPasswordForCredentials()
	{
		$db = $this->mockDb();
		$users = $this->mockUsers(); $auth = $this->mockAuth();
		$reminders = $this->mockReminders();
		$mng = $this->makeManager($db, $users, $auth, $this->mockTranslator(), null, $reminders);
		$credentials = ['username' => 'foo'];
		$input = ['password' => 'bar'];
		$token = 'baz';
		$users->shouldReceive('findByCredentials')->once()->with($credentials)->andReturn($mockUser = $this->mockUser());
		$users->shouldReceive('validPasswordReset')->with($input)->andReturn(true);
		$reminders->shouldReceive('resetUser')->once()->with($mockUser, $token, $input['password']);
		$mng->resetPasswordForCredentials($credentials, $input, $token);
	}

	protected function makeManager($db, $users, $auth, $translator, $activations = null, $reminders = null)
	{
		$manager = new \anlutro\Core\Auth\UserManager($db, $users, $auth, $translator);
		if ($activations) $manager->setActivationService($activations);
		if ($reminders) $manager->setReminderService($reminders);
		return $manager;
	}

	/**
	 * @param \Mockery\Mock $auth
	 * @param mixed         $user
	 */
	protected function pretendLogin($auth, $user)
	{
		$auth->shouldReceive('user')->andReturn($user);
	}

	protected function mockDb()
	{
		$mock = m::mock('Illuminate\Database\Connection');
		$mock->shouldReceive('transaction')->andReturnUsing(function($callback) { return $callback(m::self()); })->byDefault();
		return $mock;
	}

	protected function mockUsers()
	{
		$mock = m::mock('anlutro\Core\Auth\Users\UserRepository');
		$mock->shouldReceive('toggleExceptions')->once();
		return $mock;
	}

	protected function mockAuth()
	{
		return m::mock('Illuminate\Auth\AuthManager');
	}

	protected function mockTranslator()
	{
		$mock = m::mock('Illuminate\Translation\Translator');
		$mock->shouldReceive('get')->andReturnUsing(function($str){return $str;});
		return $mock;
	}

	public function mockActivations()
	{
		return m::mock('anlutro\Core\Auth\Activation\ActivationService');
	}

	public function mockReminders()
	{
		return m::mock('anlutro\Core\Auth\Reminders\PasswordBroker');
	}

	protected function mockUser()
	{
		return m::mock('anlutro\Core\Auth\Users\UserModel')->makePartial();
	}
}
