<?php
use Mockery as m;
use Illuminate\Support\Facades;

class AuthControllerTest extends TestCase
{
	protected $controller = 'AuthController';

	public function setUp()
	{
		parent::setUp();
		$this->repo = m::mock('c\Auth\UserRepository');
		$this->app->instance('c\Auth\UserRepository', $this->repo);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testLogin()
	{
		$this->getAction('login');

		$this->assertResponseOk();
	}

	public function testLoginSuccess()
	{
		$input = ['username' => 'foo', 'password' => 'bar', 'baz' => 'bar'];
		$credentials = array_only($input, ['username', 'password']) + ['is_active' => true];
		Facades\Auth::shouldReceive('attempt')->once()
			->with($credentials)->andReturn(true);

		$this->postAction('login', [], $input);

		$this->assertRedirectedTo('/');
		$this->assertSessionHas('success');
	}

	public function testLoginFailure()
	{
		$input = ['username' => 'foo', 'password' => 'bar', 'baz' => 'bar'];
		$credentials = array_only($input, ['username', 'password']) + ['is_active' => true];
		Facades\Auth::shouldReceive('attempt')->once()
			->with($credentials)
			->andReturn(false);

		$this->postAction('login', [], $input);

		$this->assertRedirectedToAction('login');
		$this->assertSessionHasErrors();
	}

	public function testLogout()
	{
		Facades\Auth::shouldReceive('logout')->once();

		$this->getAction('logout');

		$this->assertRedirectedToAction('login');
	}

	public function testResetStepOneForm()
	{
		$this->getAction('reminder');

		$this->assertResponseOk();
	}

	public function testResetStepOneNotFound()
	{
		$input = ['email' => 'foo', 'bar' => 'baz'];
		$credentials = array_only($input, ['email']);

		$this->swapPasswordBrokerDependencies();
		$this->repo->shouldReceive('getByCredentials')->once()
			->with($credentials)->andReturn(false);

		$this->postAction('sendReminder', [], $input);

		$this->assertRedirectedToAction('reminder');
		$this->assertSessionHasErrors();
	}

	public function testResetStepOneFailure()
	{
		$input = ['email' => 'foo', 'bar' => 'baz'];
		$credentials = array_only($input, ['email']);
		$mockUser = $this->getMockUser();

		$this->swapPasswordBrokerDependencies();
		$this->repo->shouldReceive('getByCredentials')->once()
			->with($credentials)->andReturn($mockUser);
		Facades\Password::shouldReceive('requestReset')->once()
			->with($mockUser)->andReturn(false);

		$this->postAction('sendReminder', [], $input);

		$this->assertRedirectedToAction('reminder');
		$this->assertSessionHasErrors();
	}

	public function testResetStepOneSuccess()
	{
		$input = ['email' => 'foo', 'bar' => 'baz'];
		$credentials = array_only($input, ['email']);
		$mockUser = $this->getMockUser();

		$this->swapPasswordBrokerDependencies();
		$this->repo->shouldReceive('getByCredentials')->once()
			->with($credentials)->andReturn($mockUser);
		Facades\Password::shouldReceive('requestReset')->once()
			->with($mockUser)->andReturn(true);

		$this->postAction('sendReminder', [], $input);

		$this->assertRedirectedToAction('login');
		$this->assertSessionHas('info');
	}

	public function testResetStepTwoForm()
	{
		$this->getAction('reset', [], ['token' => 'foobar']);

		$this->assertResponseOk();
	}

	public function testResetStepTwoFormWithoutToken()
	{
		$this->getAction('reset', []);

		$this->assertRedirectedToAction('login');
	}

	protected function setupResetExpectations(array $input, $result, $validates = true)
	{
		$credentials = array_only($input, ['username']);
		$token = $input['token'];
		$newPassword = $input['password'];
		
		Validator::shouldReceive('make')->once()
			->andReturn(m::mock(['fails' => (!$validates)]));
		if ($validates) {
			$this->swapPasswordBrokerDependencies();
			$user = $this->getMockUser();
			$this->repo->shouldReceive('getByCredentials')->andReturn($user);
			Facades\Password::shouldReceive('resetUser')->once()
				->with($user, $token, $newPassword)
				->andReturn($result);
		}
	}

	public function testResetValidationFails()
	{
		$input = [
			'username' => 'bar',
			'token' => 'baz',
			'password' => 'foo',
			'password_confirm' => 'bar',
			'foo' => 'baz'
		];
		$this->setupResetExpectations($input, false, false);

		$this->postAction('attemptReset', [], $input);

		$this->assertRedirectedToAction('reset', ['token' => 'baz']);
		$this->assertSessionHasErrors();
	}

	public function testResetFailure()
	{
		$input = [
			'username' => 'bar',
			'token' => 'baz',
			'password' => 'foo',
			'password_confirm' => 'bar',
			'foo' => 'baz'
		];
		$this->setupResetExpectations($input, false);

		$this->postAction('attemptReset', [], $input);

		$this->assertRedirectedTo('login');
		$this->assertSessionHasErrors();
	}

	public function testResetSuccess()
	{
		$input = [
			'username' => 'bar',
			'token' => 'baz',
			'password' => 'foo',
			'password_confirm' => 'bar',
			'foo' => 'baz'
		];
		$this->setupResetExpectations($input, true);

		$this->postAction('attemptReset', [], $input);

		$this->assertRedirectedTo('login');
		$this->assertSessionHas('success');
	}

	public function swapPasswordBrokerDependencies()
	{
		$this->app['auth.reminder.repository'] = m::mock('Illuminate\Auth\Reminders\ReminderRepositoryInterface');
	}

	protected function getMockUser()
	{
		return m::mock('App\User')->makePartial();
	}
}
