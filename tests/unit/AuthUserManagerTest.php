<?php

use Mockery as m;

class AuthUserManagerTest extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		m::close();
	}

	public function testCreateWithSufficientAccess()
	{
		$users = $this->mockUsers();
		$auth = $this->mockAuth();
		$mng = $this->makeManager($users, $auth, $this->mockTranslator());
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
		$users = $this->mockUsers();
		$auth = $this->mockAuth();
		$mng = $this->makeManager($users, $auth, $this->mockTranslator());
		$this->pretendLogin($auth, $user = $this->mockUser());
		$user->access_level = 50;

		$input = ['username' => 'foobar', 'user_level' => 100];

		$result = $mng->create($input);
	}

	public function testCreateRepositoryFails()
	{
		$users = $this->mockUsers();
		$auth = $this->mockAuth();
		$mng = $this->makeManager($users, $auth, $this->mockTranslator());
		$user = $this->mockUser(); $user->user_level = 100;
		$this->pretendLogin($auth, $user);

		$input = ['username' => 'foobar', 'user_level' => '1'];
		$users->shouldReceive('createAsAdmin')->once()->with($input)
			->andReturn(false);
		$users->shouldReceive('getErrors')->once()->andReturn('foo');

		$this->assertFalse($mng->create($input));
		$this->assertEquals('foo', $mng->getErrors());
	}

	public function testCreateAndSendActivation()
	{
		$users = $this->mockUsers();
		$auth = $this->mockAuth();
		$activations = $this->mockActivations();
		$mng = $this->makeManager($users, $auth, $this->mockTranslator(), $activations);
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
		$users = $this->mockUsers();
		$auth = $this->mockAuth();
		$mng = $this->makeManager($users, $auth, $this->mockTranslator());
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
		$users = $this->mockUsers();
		$auth = $this->mockAuth();
		$activations = $this->mockActivations();
		$mng = $this->makeManager($users, $auth, $this->mockTranslator(), $activations);

		$input = ['username' => 'foobar', 'user_level' => '20', 'user_type' => 'asdf', 'is_active' => '1'];
		$users->shouldReceive('create')->once()->with($input)
			->andReturn($mockUser = $this->mockUser());
		$activations->shouldReceive('generate')->once()->with($mockUser);

		$this->assertSame($mockUser, $mng->register($input));
	}

	public function testUpdateCurrentProfileSuccess()
	{
		$users = $this->mockUsers(); $auth = $this->mockAuth();
		$mng = $this->makeManager($users, $auth, $this->mockTranslator());
		$input = ['foo' => 'bar', 'old_password' => 'baz'];
		$this->pretendLogin($auth, $mockUser = $this->mockUser());
		$mockUser->shouldReceive('confirmPassword')->once()->with('baz')->andReturn(true);
		$users->shouldReceive('update')->once()->with($mockUser, $input)->andReturn(true);
		$this->assertTrue($mng->updateCurrentProfile($input));
	}

	public function testUpdateCurrentProfileIncorrectPassword()
	{
		$users = $this->mockUsers(); $auth = $this->mockAuth();
		$mng = $this->makeManager($users, $auth, $this->mockTranslator());
		$input = ['foo' => 'bar', 'old_password' => 'baz'];
		$this->pretendLogin($auth, $mockUser = $this->mockUser());
		$mockUser->shouldReceive('confirmPassword')->once()->with('baz')->andReturn(false);
		$users->shouldReceive('getErrors->add')->once()->with('c::auth.invalid-password');
		$this->assertFalse($mng->updateCurrentProfile($input));
	}

	public function testUpdateCurrentProfileValidationFails()
	{
		$users = $this->mockUsers(); $auth = $this->mockAuth();
		$mng = $this->makeManager($users, $auth, $this->mockTranslator());
		$input = ['foo' => 'bar', 'old_password' => 'baz'];
		$this->pretendLogin($auth, $mockUser = $this->mockUser());
		$mockUser->shouldReceive('confirmPassword')->once()->with('baz')->andReturn(true);
		$users->shouldReceive('update')->once()->with($mockUser, $input)->andReturn(false);
		$this->assertFalse($mng->updateCurrentProfile($input));
	}

	public function testLogin()
	{
		$users = $this->mockUsers(); $auth = $this->mockAuth();
		$mng = $this->makeManager($users, $auth, $this->mockTranslator());
		$input = ['username' => 'foo', 'password' => 'bar'];
		$auth->shouldReceive('attempt')->once()->with($input + ['is_active' => 1])->andReturn(true);
		$auth->shouldReceive('getUser->rehashPassword')->once()->with('bar');
		$mng->login($input);
	}

	public function testRequestPasswordResetForEmail()
	{
		$users = $this->mockUsers(); $auth = $this->mockAuth();
		$reminders = $this->mockReminders();
		$mng = $this->makeManager($users, $auth, $this->mockTranslator(), null, $reminders);
		$users->shouldReceive('findByCredentials')->once()->with(['email' => 'foo@bar.com'])->andReturn($mockUser = $this->mockUser());
		$reminders->shouldReceive('requestReset')->once()->with($mockUser)->andReturn(true);
		$this->assertTrue($mng->requestPasswordResetForEmail('foo@bar.com'));
	}

	public function testResetPasswordForCredentials()
	{
		$users = $this->mockUsers(); $auth = $this->mockAuth();
		$reminders = $this->mockReminders();
		$mng = $this->makeManager($users, $auth, $this->mockTranslator(), null, $reminders);
		$credentials = ['username' => 'foo'];
		$input = ['password' => 'bar'];
		$token = 'baz';
		$users->shouldReceive('findByCredentials')->once()->with($credentials)->andReturn($mockUser = $this->mockUser());
		$users->shouldReceive('valid')->once()->with('passwordReset', $input)->andReturn(true);
		$reminders->shouldReceive('resetUser')->once()->with($mockUser, $token, $input['password']);
		$mng->resetPasswordForCredentials($credentials, $input, $token);
	}

	protected function makeManager($users, $auth, $translator, $activations = null, $reminders = null)
	{
		$manager = new anlutro\Core\Auth\UserManager($users, $auth, $translator);
		if ($activations) $manager->setActivationService($activations);
		if ($reminders) $manager->setReminderService($reminders);
		return $manager;
	}

	protected function pretendLogin($auth, $user)
	{
		$auth->shouldReceive('getUser')->andReturn($user);
	}

	protected function mockUsers()
	{
		return m::mock('anlutro\Core\Auth\Users\UserRepository');
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
