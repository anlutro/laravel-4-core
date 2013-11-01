<?php
use Mockery as m;
use c\Auth\PasswordBroker;
use Illuminate\Support\Facades;

class PasswordBrokerTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->users = m::mock('Illuminate\Auth\UserProviderInterface');
		$this->reminders = m::mock('Illuminate\Auth\Reminders\ReminderRepositoryInterface');
		$this->broker = new PasswordBroker($this->users, $this->reminders);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testFindUser()
	{
		$credentials = ['foo' => 'bar'];
		$this->users->shouldReceive('retrieveByCredentials')->once()
			->with($credentials)->andReturn('baz');
		$result = $this->broker->findUser($credentials);
		$this->assertEquals('baz', $result);
	}

	public function testRequestReset()
	{
		$user = $this->getMockUser();
		$this->reminders->shouldReceive('create')->with($user)->once();
		$this->setupMailExpectations();

		$this->assertTrue($this->broker->requestReset($user));
	}

	public function testRequestResetQueue()
	{
		$user = $this->getMockUser();
		$this->reminders->shouldReceive('create')->with($user)->once();
		$this->setupMailExpectations('queue');

		$this->assertTrue($this->broker->requestReset($user));
	}

	public function testResetUserNotFound()
	{
		$credentials = ['username' => 'foo'];
		$token = 'bar'; $newPassword = 'baz';
		$this->users->shouldReceive('retrieveByCredentials')->with($credentials)
			->once()->andReturn(false);

		// this may throw an exception in the future
		$this->assertFalse($this->broker->reset($credentials, $token, $newPassword));
	}

	public function testResetReminderNotFound()
	{
		$credentials = ['username' => 'foo'];
		$token = 'bar'; $newPassword = 'baz';
		$user = $this->getMockUser();
		$this->users->shouldReceive('retrieveByCredentials')->with($credentials)
			->once()->andReturn($user);
		$this->reminders->shouldReceive('exists')->with($user, $token)
			->once()->andReturn(false);

		// this may throw an exception in the future
		$this->assertFalse($this->broker->reset($credentials, $token, $newPassword));
	}

	public function testResetSuccess()
	{
		$credentials = ['username' => 'foo'];
		$token = 'bar'; $newPassword = 'baz';
		$user = $this->getMockUser();
		$this->users->shouldReceive('retrieveByCredentials')->with($credentials)
			->once()->andReturn($user);
		$this->reminders->shouldReceive('exists')->with($user, $token)
			->once()->andReturn(true);
		$user->shouldReceive('setPasswordAttribute')->with($newPassword)->once();
		$user->shouldReceive('save')->once();
		$this->reminders->shouldReceive('delete')->with($token)->once();

		$this->assertEquals($user, $this->broker->reset($credentials, $token, $newPassword));
	}

	protected function setupMailExpectations($method = 'mail', $success = true)
	{
		Facades\Config::shouldReceive('get')->with('auth.reminder.email');
		$queue = ($method == 'mail') ? false : true;
		$method = $queue ? 'queue' : 'mail';
		Facades\Config::shouldReceive('get')->with('auth.reminder.queue')
			->once()->andReturn($queue);
		Facades\Mail::shouldReceive($method)->once()->andReturn($success);
	}

	protected function getMockUser($email = 'test@example.com')
	{
		$mock = m::mock('Illuminate\Auth\Reminders\RemindableInterface');
		$mock->shouldReceive('getReminderEmail')->andReturn($email);
		return $mock;
	}
}
