<?php
use Mockery as m;
use c\Auth\Reminders\PasswordBroker;
use Illuminate\Support\Facades;

class PasswordBrokerTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->users = m::mock('Illuminate\Auth\UserProviderInterface');
		$this->reminders = m::mock('Illuminate\Auth\Reminders\ReminderRepositoryInterface');
		$this->mailer = m::mock('Illuminate\Mail\Mailer');
		$this->config = ['email-view' => 'view', 'queue-email' => false];
	}

	public function getBroker()
	{
		return new PasswordBroker($this->users, $this->reminders, $this->mailer, $this->config);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testRequestReset()
	{
		$user = $this->getMockUser();
		$this->reminders->shouldReceive('create')->with($user)->once();
		$this->setupMailExpectations();

		$this->assertTrue($this->getBroker()->requestReset($user));
	}

	public function testRequestResetQueue()
	{
		$user = $this->getMockUser();
		$this->reminders->shouldReceive('create')->with($user)->once();
		$this->setupMailExpectations('queue');

		$this->assertTrue($this->getBroker()->requestReset($user));
	}

	public function testResetUserSuccess()
	{
		$credentials = ['username' => 'foo'];
		$token = 'bar'; $newPassword = 'baz';
		$user = $this->getMockUser();
		$this->reminders->shouldReceive('exists')->once()
			->with($user, $token)->andReturn(true);
		$user->shouldReceive('setPasswordAttribute')->once()->with($newPassword);
		$user->shouldReceive('save')->once();
		$this->reminders->shouldReceive('delete')->once()->with($token);

		$this->assertTrue($this->getBroker()->resetUser($user, $token, $newPassword));
	}

	public function testResetUserFailure()
	{
		$credentials = ['username' => 'foo'];
		$token = 'bar'; $newPassword = 'baz';
		$user = $this->getMockUser();
		$this->reminders->shouldReceive('exists')->once()
			->with($user, $token)->andReturn(false);

		$this->assertFalse($this->getBroker()->resetUser($user, $token, $newPassword));
	}
	
	protected function setupMailExpectations($method = 'mail', $success = true)
	{
		$queue = ($method == 'mail') ? false : true;
		$method = $queue ? 'queue' : 'mail';
		$this->config['queue-email'] = $queue;
		$this->mailer->shouldReceive($method)->once()->andReturn($success);
	}

	protected function getMockUser($email = 'test@example.com')
	{
		$mock = m::mock('Illuminate\Auth\Reminders\RemindableInterface');
		$mock->shouldReceive('getReminderEmail')->andReturn($email);
		return $mock;
	}
}
