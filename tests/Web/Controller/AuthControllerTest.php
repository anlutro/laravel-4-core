<?php
namespace anlutro\Core\Tests\Web\Controller;

use Mockery as m;
use Illuminate\Support\Facades;
use anlutro\Core\Auth\Activation\Activation;
use anlutro\Core\Auth\Reminders\ReminderException;

/** @medium */
class AuthControllerTest extends AuthControllerTestCase
{
	protected $controller = 'anlutro\Core\Web\AuthController';

	public function testLogin()
	{
		$this->manager->shouldReceive('remindersEnabled')->once()->andReturn(false);

		$this->getAction('login');

		$this->assertResponseOk();
		$this->checkForMissingTranslations();
	}

	public function testLoginSuccess()
	{
		$input = ['username' => 'foo', 'password' => 'bar', 'baz' => 'bar'];
		$this->setupLoginExpectations($input, true);

		$this->postAction('login', [], $input);

		$this->assertRedirectedTo('/');
		$this->assertSessionHas('success');
		$this->checkForMissingTranslations();
	}

	public function testLoginFailure()
	{
		$input = ['username' => 'foo', 'password' => 'bar', 'baz' => 'bar'];
		$this->setupLoginExpectations($input, false);

		$this->postAction('login', [], $input);

		$this->assertRedirectedToAction('login');
		$this->assertSessionHas('error');
		$this->checkForMissingTranslations();
	}

	public function testLogout()
	{
		$this->manager->shouldReceive('logout')->andReturn(false);

		$this->getAction('logout');

		$this->assertRedirectedToAction('login');
		$this->checkForMissingTranslations();
	}

	public function testRegisterView()
	{
		$this->manager->shouldReceive('setActivationService')->once();
		$this->app->register('anlutro\Core\Auth\Activation\ActivationServiceProvider');

		$this->manager->shouldReceive('getNew')->andReturn($this->getMockUser());

		$this->getAction('register');

		$this->assertResponseOk();
		$this->checkForMissingTranslations();
	}

	public function testRegisterSubmit()
	{
		$this->setupRegisterExpectations($input = ['foo' => 'bar']);

		$this->postAction('attemptRegistration', [], $input);
		$this->assertRedirectedToAction('login');
		$this->assertSessionHas('success');
		$this->checkForMissingTranslations();
	}

	/** @test */
	public function registerSubmitValidationFails()
	{
		$this->setupRegisterExpectations($input = ['foo' => 'bar'], new \anlutro\LaravelValidation\ValidationException([]));

		$this->postAction('attemptRegistration', [], $input);
		$this->assertRedirectedToAction('register');
		$this->assertSessionHasErrors();
		$this->checkForMissingTranslations();
	}

	/** @test */
	public function registerSubmitActivationFails()
	{
		$this->setupRegisterExpectations($input = ['foo' => 'bar'], new \anlutro\Core\Auth\Activation\ActivationException());

		$this->postAction('attemptRegistration', [], $input);
		$this->assertRedirectedToAction('register');
		$this->assertSessionHas('error');
		$this->checkForMissingTranslations();
	}

	public function testActivationSuccess()
	{
		$this->setupActivationExpectations('foo', true);

		$this->getAction('activate', ['activation_code' => 'foo']);

		$this->assertRedirectedToAction('anlutro\Core\Web\AuthController@login');
		$this->assertSessionHas('success');
		$this->checkForMissingTranslations();
	}

	public function testActivationFailure()
	{
		$this->setupActivationExpectations('foo', false);

		$this->getAction('activate', ['activation_code' => 'foo']);

		$this->assertRedirectedToAction('anlutro\Core\Web\AuthController@login');
		$this->assertSessionHas('error');
		$this->checkForMissingTranslations();
	}

	public function testResetStepOneForm()
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('anlutro\Core\Auth\Reminders\ReminderServiceProvider');

		$this->getAction('reminder');

		$this->assertResponseOk();
		$this->checkForMissingTranslations();
	}

	public function testResetStepOneFailure()
	{
		$input = ['email' => 'foo@bar.com', 'bar' => 'baz'];
		$this->setupRequestResetExpectations('foo@bar.com', false);

		$this->postAction('sendReminder', [], $input);

		$this->assertRedirectedToAction('reminder');
		$this->assertSessionHas('error');
		$this->checkForMissingTranslations();
	}

	public function testResetStepOneSuccess()
	{
		$input = ['email' => 'foo@bar.com', 'bar' => 'baz'];
		$this->setupRequestResetExpectations('foo@bar.com', true);

		$this->postAction('sendReminder', [], $input);

		$this->assertRedirectedToAction('login');
		$this->assertSessionHas('info');
		$this->checkForMissingTranslations();
	}

	public function testResetStepTwoForm()
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('anlutro\Core\Auth\Reminders\ReminderServiceProvider');

		$this->getAction('reset', [], ['token' => 'foobar']);

		$this->assertResponseOk();
		$this->checkForMissingTranslations();
	}

	public function testResetStepTwoFormWithoutToken()
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('anlutro\Core\Auth\Reminders\ReminderServiceProvider');

		$this->getAction('reset', []);

		$this->assertRedirectedToAction('login');
		$this->checkForMissingTranslations();
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

		$this->postAction('attemptReset', [], $input);

		$this->assertRedirectedToAction('login');
		$this->assertSessionHas('error');
		$this->checkForMissingTranslations();
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

		$this->postAction('attemptReset', [], $input);

		$this->assertRedirectedToAction('login');
		$this->assertSessionHas('success');
		$this->checkForMissingTranslations();
	}

	protected function getMockUser()
	{
		return m::mock('anlutro\Core\Auth\Users\UserModel')->makePartial();
	}
}
