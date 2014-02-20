<?php
use Mockery as m;
use Illuminate\Support\Facades;
use c\Auth\Activation\Activation;

class AuthControllerTest extends AppTestCase
{
	protected $controller = 'AuthController';

	public function setUp()
	{
		parent::setUp();
		$this->manager = m::mock('c\Auth\UserManager');
		$this->app->instance('c\Auth\UserManager', $this->manager);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testLogin()
	{
		$this->manager->shouldReceive('remindersEnabled')->once()->andReturn(false);

		$this->getAction('login');

		$this->assertResponseOk();
	}

	public function testLoginSuccess()
	{
		$input = ['username' => 'foo', 'password' => 'bar', 'baz' => 'bar'];
		$credentials = array_only($input, ['username', 'password']);
		$this->manager->shouldReceive('login')->with($credentials)->andReturn(true);

		$this->postAction('login', [], $input);

		$this->assertRedirectedTo('/');
		$this->assertSessionHas('success');
	}

	public function testLoginFailure()
	{
		$input = ['username' => 'foo', 'password' => 'bar', 'baz' => 'bar'];
		$credentials = array_only($input, ['username', 'password']);
		$this->manager->shouldReceive('login')->with($credentials)->andReturn(false);

		$this->postAction('login', [], $input);

		$this->assertRedirectedToAction('login');
		$this->assertSessionHasErrors();
	}

	public function testLogout()
	{
		$this->manager->shouldReceive('logout')->andReturn(false);

		$this->getAction('logout');

		$this->assertRedirectedToAction('login');
	}

	public function testRegisterView()
	{
		$this->manager->shouldReceive('setActivationService')->once();
		$this->app->register('c\Auth\Activation\ActivationServiceProvider');

		$this->manager->shouldReceive('getNew')->andReturn($this->getMockUser());

		$this->getAction('register');

		$this->assertResponseOk();
	}

	public function testRegisterSubmit()
	{
		$this->manager->shouldReceive('setActivationService')->once();
		$this->app->register('c\Auth\Activation\ActivationServiceProvider');

		$input = ['foo' => 'bar'];
		$this->manager->shouldReceive('register')->once()
			->with($input)->andReturn(true);

		$this->postAction('attemptRegistration', [], $input);

		$this->assertRedirectedToAction('login');
		$this->assertSessionHas('success');
	}

	public function testActivation()
	{
		$this->manager->shouldReceive('setActivationService')->once();
		$this->app->register('c\Auth\Activation\ActivationServiceProvider');

		$this->manager->shouldReceive('activateByCode')->with('foo')->once()->andReturn(true);
		
		$this->getAction('activate', ['activation_code' => 'foo']);

		$this->assertRedirectedToAction('AuthController@login');
	}

	public function testResetStepOneForm()
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('c\Auth\Reminders\ReminderServiceProvider');

		$this->getAction('reminder');

		$this->assertResponseOk();
	}

	public function testResetStepOneFailure()
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('c\Auth\Reminders\ReminderServiceProvider');

		$input = ['email' => 'foo', 'bar' => 'baz'];
		$this->manager->shouldReceive('requestPasswordResetForEmail')->once()
			->with($input['email'])->andReturn(false);

		$this->postAction('sendReminder', [], $input);

		$this->assertRedirectedToAction('reminder');
		$this->assertSessionHasErrors();
	}

	public function testResetStepOneSuccess()
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('c\Auth\Reminders\ReminderServiceProvider');

		$input = ['email' => 'foo', 'bar' => 'baz'];
		$mockUser = $this->getMockUser();
		$this->manager->shouldReceive('requestPasswordResetForEmail')->once()
			->with($input['email'])->andReturn(true);

		$this->postAction('sendReminder', [], $input);

		$this->assertRedirectedToAction('login');
		$this->assertSessionHas('info');
	}

	public function testResetStepTwoForm()
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('c\Auth\Reminders\ReminderServiceProvider');

		$this->getAction('reset', [], ['token' => 'foobar']);

		$this->assertResponseOk();
	}

	public function testResetStepTwoFormWithoutToken()
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('c\Auth\Reminders\ReminderServiceProvider');

		$this->getAction('reset', []);

		$this->assertRedirectedToAction('login');
	}

	protected function setupResetExpectations(array $input, $result)
	{
		$credentials = array_only($input, ['username']);
		$passwords = array_only($input, ['password', 'password_confirmation']);
		$token = $input['token'];
		$this->manager->shouldReceive('resetPasswordForCredentials')->once()
			->with($credentials, $passwords, $token)->andReturn($result);
	}

	public function testResetFailure()
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('c\Auth\Reminders\ReminderServiceProvider');

		$input = [
			'username' => 'bar',
			'token' => 'baz',
			'password' => 'foo',
			'password_confirmation' => 'bar',
			'foo' => 'baz'
		];
		$this->setupResetExpectations($input, false);

		$this->postAction('attemptReset', [], $input);

		$this->assertRedirectedTo('login');
		$this->assertSessionHasErrors();
	}

	public function testResetSuccess()
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('c\Auth\Reminders\ReminderServiceProvider');

		$input = [
			'username' => 'bar',
			'token' => 'baz',
			'password' => 'foo',
			'password_confirmation' => 'bar',
			'foo' => 'baz'
		];
		$this->setupResetExpectations($input, true);

		$this->postAction('attemptReset', [], $input);

		$this->assertRedirectedTo('login');
		$this->assertSessionHas('success');
	}

	protected function getMockUser()
	{
		return m::mock('c\Auth\UserModel')->makePartial();
	}
}
