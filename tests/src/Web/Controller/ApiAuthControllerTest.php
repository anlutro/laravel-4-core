<?php
namespace anlutro\Core\Tests\Web\Controller;

use Mockery as m;
use anlutro\Core\Tests\AppTestCase;

class ApiAuthControllerTest extends AppTestCase
{
	protected $controller = 'anlutro\Core\Web\AuthController';

	public function setUp()
	{
		parent::setUp();
		$this->manager = m::mock('anlutro\Core\Auth\UserManager');
		$this->app->instance('anlutro\Core\Auth\UserManager', $this->manager);

		// instead of setting up the filters and having to deal with auth/csrf, just
		// bind the controllers manually
		$this->app->bind('anlutro\Core\Web\AuthController', 'anlutro\Core\Web\ApiAuthController');
		$this->app->bind('anlutro\Core\Web\UserController', 'anlutro\Core\Web\ApiUserController');

		// mock a JSON/AJAX request
		$this->client->setServerParameter('HTTP_X-Requested-With', 'XMLHttpRequest');
		$this->client->setServerParameter('HTTP_CONTENT_TYPE', 'application/json');
		$this->client->setServerParameter('HTTP_ACCEPT', 'application/json');
	}

	public function tearDown()
	{
		m::close();
	}

	public function assertResponseJson($response)
	{
		$this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
		return $response->getData();
	}

	public function assertResponse200($response)
	{
		$actual = $response->getStatusCode();
		$this->assertTrue($response->isOk(), 'Expected status code 200, got '.$actual);
	}

	public function testLoginSuccess()
	{
		$input = ['username' => 'foo', 'password' => 'bar', 'baz' => 'bar'];
		$credentials = array_only($input, ['username', 'password']);
		$this->manager->shouldReceive('login')->with($credentials)->andReturn(true);
		$this->manager->shouldReceive('getCurrentUser')->andReturn('foo');

		$response = $this->postAction('attemptLogin', [], $input);

		$this->assertResponse200($response);
		$data = $this->assertResponseJson($response);
		$this->assertEquals('foo', $data->user);
	}

	public function testLoginFailure()
	{
		$input = ['username' => 'foo', 'password' => 'bar', 'baz' => 'bar'];
		$credentials = array_only($input, ['username', 'password']);
		$this->manager->shouldReceive('login')->with($credentials)->andReturn(false);

		$response = $this->postAction('attemptLogin', [], $input);

		$data = $this->assertResponseJson($response);
		$this->assertEquals(401, $response->getStatusCode());
	}

	public function testLogout()
	{
		$this->manager->shouldReceive('logout')->andReturn(false);

		$response = $this->getAction('logout');

		$this->assertEquals(403, $response->getStatusCode());
	}

	protected function setupRegister(array $input, \Exception $exception = null)
	{
		$this->manager->shouldReceive('setActivationService')->once();
		$this->app->register('anlutro\Core\Auth\Activation\ActivationServiceProvider');
		$expectation = $this->manager->shouldReceive('register')->once()->with($input);
		if ($exception) {
			$expectation->andThrow($exception);
		}
	}

	public function testRegister()
	{
		$this->setupRegister($input = ['foo' => 'bar']);

		$response = $this->postAction('attemptRegistration', [], $input);

		$this->assertResponse200($response);
		$data = $this->assertResponseJson($response);
	}

	/** @test */
	public function registerValidationFails()
	{
		$this->setupRegister($input = ['foo' => 'bar'], new \anlutro\LaravelValidation\ValidationException([]));
		
		$response = $this->postAction('attemptRegistration', [], $input);

		$data = $this->assertResponseJson($response);
		$this->assertEquals(400, $response->getStatusCode());
	}

	/** @test */
	public function registerActivationFails()
	{
		$this->setupRegister($input = ['foo' => 'bar'], new \anlutro\Core\Auth\Activation\ActivationException());

		$response = $this->postAction('attemptRegistration', [], $input);
		
		$data = $this->assertResponseJson($response);
		$this->assertEquals(400, $response->getStatusCode());
	}

	public function testActivation()
	{
		$this->manager->shouldReceive('setActivationService')->once();
		$this->app->register('anlutro\Core\Auth\Activation\ActivationServiceProvider');
		$this->manager->shouldReceive('activateByCode')->with('foo')->once()->andReturn(true);
		
		$response = $this->getAction('activate', ['activation_code' => 'foo']);

		$this->assertResponse200($response);
		$data = $this->assertResponseJson($response);
	}

	public function testResetStepOneFailure()
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('anlutro\Core\Auth\Reminders\ReminderServiceProvider');

		$input = ['email' => 'foo', 'bar' => 'baz'];
		$this->manager->shouldReceive('requestPasswordResetForEmail')->once()
			->with($input['email'])->andReturn(false);

		$response = $this->postAction('sendReminder', [], $input);

		$data = $this->assertResponseJson($response);
		$this->assertEquals(400, $response->getStatusCode());
	}

	public function testResetStepOneSuccess()
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('anlutro\Core\Auth\Reminders\ReminderServiceProvider');

		$input = ['email' => 'foo', 'bar' => 'baz'];
		$mockUser = $this->getMockUser();
		$this->manager->shouldReceive('requestPasswordResetForEmail')->once()
			->with($input['email'])->andReturn(true);

		$response = $this->postAction('sendReminder', [], $input);

		$this->assertResponse200($response);
		$data = $this->assertResponseJson($response);
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
		$this->app->register('anlutro\Core\Auth\Reminders\ReminderServiceProvider');

		$input = [
			'username' => 'bar',
			'token' => 'baz',
			'password' => 'foo',
			'password_confirmation' => 'bar',
			'foo' => 'baz'
		];
		$this->setupResetExpectations($input, false);
		$this->manager->shouldReceive('getErrors')->once()->andReturn([]);

		$response = $this->postAction('attemptReset', [], $input);

		$data = $this->assertResponseJson($response);
		$this->assertEquals(400, $response->getStatusCode());
		$this->assertEquals(['password could not be reset'], $data->errors);
	}

	public function testResetSuccess()
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('anlutro\Core\Auth\Reminders\ReminderServiceProvider');

		$input = [
			'username' => 'bar',
			'token' => 'baz',
			'password' => 'foo',
			'password_confirmation' => 'bar',
			'foo' => 'baz'
		];
		$this->setupResetExpectations($input, true);

		$response = $this->postAction('attemptReset', [], $input);

		$this->assertResponse200($response);
		$data = $this->assertResponseJson($response);
	}

	protected function getMockUser()
	{
		return m::mock('anlutro\Core\Auth\Users\UserModel')->makePartial();
	}
}
