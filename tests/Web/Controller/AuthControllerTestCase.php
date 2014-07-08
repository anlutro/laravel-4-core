<?php
namespace anlutro\Core\Tests\Web\Controller;

use Mockery as m;
use Illuminate\Support\Facades;
use anlutro\Core\Auth\AuthenticationException;
use anlutro\Core\Auth\Activation\ActivationException;
use anlutro\Core\Auth\Reminders\ReminderException;
use anlutro\Core\Tests\AppTestCase;

/**
 * Abstract class with shared behaviour between API and regular auth controller
 * to make it easier to keep test expectations consistent.
 */
abstract class AuthControllerTestCase extends AppTestCase
{
	protected $manager;

	public function setUp()
	{
		parent::setUp();
		$this->manager = m::mock('anlutro\Core\Auth\UserManager');
		$this->app->instance('anlutro\Core\Auth\UserManager', $this->manager);
	}

	protected function setupLoginExpectations(array $input, $result)
	{
		$credentials = array_only($input, ['username', 'password']);
		$remember = isset($input['remember_me']) && $input['remember_me'];
		$expectation = $this->manager->shouldReceive('login')->with($credentials, $remember)->once();
		if ($result) {
			$expectation->andReturn(true);
		} else {
			$expectation->andThrow(new AuthenticationException);
		}
	}

	protected function setupRegisterExpectations(array $input, \Exception $exception = null)
	{
		$this->manager->shouldReceive('setActivationService')->once();
		$this->app->register('anlutro\Core\Auth\Activation\ActivationServiceProvider');
		$expectation = $this->manager->shouldReceive('register')->once()->with($input);
		if ($exception) {
			$expectation->andThrow($exception);
		}
	}

	protected function setupActivationExpectations($code, $result)
	{
		$this->manager->shouldReceive('setActivationService')->once();
		$this->app->register('anlutro\Core\Auth\Activation\ActivationServiceProvider');

		$expectation = $this->manager->shouldReceive('activateByCode')->with($code)->once();
		if ($result) {
			$expectation->andReturn(true);
		} else {
			$expectation->andThrow(new ActivationException);
		}
	}

	protected function setupRequestResetExpectations($email, $result)
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('anlutro\Core\Auth\Reminders\ReminderServiceProvider');

		$expectation = $this->manager->shouldReceive('requestPasswordResetForEmail')->with($email)->once();
		if ($result) {
			$expectation->andReturn(true);
		} else {
			$expectation->andThrow(new ReminderException);
		}
	}

	protected function setupResetPasswordExpectations(array $input, $result)
	{
		$this->manager->shouldReceive('setReminderService')->once();
		$this->app->register('anlutro\Core\Auth\Reminders\ReminderServiceProvider');

		$credentials = array_only($input, ['username']);
		$passwords = array_only($input, ['password', 'password_confirmation']);
		$token = $input['token'];
		$expectation = $this->manager->shouldReceive('resetPasswordForCredentials')
			->once()->with($credentials, $passwords, $token);
		if ($result) {
			$expectation->andReturn(true);
		} else {
			$expectation->andThrow(new ReminderException);
		}
	}
}