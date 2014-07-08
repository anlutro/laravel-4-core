<?php
namespace anlutro\Core\Tests\Web\Controller;

use Mockery as m;

/** @medium */
class ApiAuthControllerTest extends AuthControllerTestCase
{
	protected $controller = 'anlutro\Core\Web\AuthController';

	public function setUp()
	{
		parent::setUp();

		// instead of setting up the filters and having to deal with auth/csrf, just
		// bind the controllers manually
		$this->app->bind('anlutro\Core\Web\AuthController', 'anlutro\Core\Web\ApiAuthController');
		$this->app->bind('anlutro\Core\Web\UserController', 'anlutro\Core\Web\ApiUserController');

		// mock a JSON/AJAX request
		$this->client->setServerParameter('HTTP_X-Requested-With', 'XMLHttpRequest');
		$this->client->setServerParameter('HTTP_CONTENT_TYPE', 'application/json');
		$this->client->setServerParameter('HTTP_ACCEPT', 'application/json');
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
		$input = ['username' => 'foo', 'password' => 'bar'];
		$this->setupLoginExpectations($input, true);
		$this->manager->shouldReceive('getCurrentUser')->once()->andReturn('foo');

		$response = $this->postAction('attemptLogin', [], $input);

		$this->assertResponse200($response);
		$data = $this->assertResponseJson($response);
		$this->assertEquals('foo', $data->user);
	}

	public function testLoginFailure()
	{
		$input = ['username' => 'foo', 'password' => 'bar'];
		$this->setupLoginExpectations($input, false);

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

	public function testRegister()
	{
		$this->setupRegisterExpectations($input = ['foo' => 'bar']);

		$response = $this->postAction('attemptRegistration', [], $input);

		$this->assertResponse200($response);
		$data = $this->assertResponseJson($response);
	}

	/** @test */
	public function registerValidationFails()
	{
		$this->setupRegisterExpectations($input = ['foo' => 'bar'], new \anlutro\LaravelValidation\ValidationException([]));
		
		$response = $this->postAction('attemptRegistration', [], $input);

		$data = $this->assertResponseJson($response);
		$this->assertEquals(400, $response->getStatusCode());
	}

	/** @test */
	public function registerActivationFails()
	{
		$this->setupRegisterExpectations($input = ['foo' => 'bar'], new \anlutro\Core\Auth\Activation\ActivationException());

		$response = $this->postAction('attemptRegistration', [], $input);
		
		$data = $this->assertResponseJson($response);
		$this->assertEquals(400, $response->getStatusCode());
	}

	public function testActivationSuccess()
	{
		$this->setupActivationExpectations('foo', true);
		
		$response = $this->getAction('activate', ['activation_code' => 'foo']);

		$this->assertResponse200($response);
		$data = $this->assertResponseJson($response);
	}

	public function testActivationFailure()
	{
		$this->setupActivationExpectations('foo', false);
		
		$response = $this->getAction('activate', ['activation_code' => 'foo']);

		$data = $this->assertResponseJson($response);
		$this->assertEquals(400, $response->getStatusCode());
	}

	public function testResetStepOneFailure()
	{
		$input = ['email' => 'foo@bar.com', 'bar' => 'baz'];
		$this->setupRequestResetExpectations('foo@bar.com', false);

		$response = $this->postAction('sendReminder', [], $input);

		$data = $this->assertResponseJson($response);
		$this->assertEquals(400, $response->getStatusCode());
	}

	public function testResetStepOneSuccess()
	{
		$input = ['email' => 'foo@bar.com', 'bar' => 'baz'];
		$this->setupRequestResetExpectations('foo@bar.com', true);

		$response = $this->postAction('sendReminder', [], $input);

		$this->assertResponse200($response);
		$data = $this->assertResponseJson($response);
	}

	public function testResetFailure()
	{
		$input = [
			'username' => 'bar',
			'token' => 'baz',
			'password' => 'foo',
			'password_confirmation' => 'bar',
			'foo' => 'baz'
		];
		$this->setupResetPasswordExpectations($input, false);

		$response = $this->postAction('attemptReset', [], $input);

		$data = $this->assertResponseJson($response);
		$this->assertEquals(400, $response->getStatusCode());
		$this->assertEquals(['password could not be reset'], $data->errors);
	}

	public function testResetSuccess()
	{
		$input = [
			'username' => 'bar',
			'token' => 'baz',
			'password' => 'foo',
			'password_confirmation' => 'bar',
			'foo' => 'baz'
		];
		$this->setupResetPasswordExpectations($input, true);

		$response = $this->postAction('attemptReset', [], $input);

		$this->assertResponse200($response);
		$data = $this->assertResponseJson($response);
	}

	protected function getMockUser()
	{
		return m::mock('anlutro\Core\Auth\Users\UserModel')->makePartial();
	}
}
