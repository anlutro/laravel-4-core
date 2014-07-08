<?php
namespace anlutro\Core\Tests\Auth\Reminders;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use anlutro\Core\Auth\Reminders\PasswordBroker;
use Illuminate\Support\Facades;

/** @small */
class PasswordBrokerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var \Mockery\Mock
	 */
	protected $users;

	/**
	 * @var \Mockery\Mock
	 */
	protected $reminders;

	/**
	 * @var \Mockery\Mock
	 */
	protected $mailer;

	/**
	 * @var \Mockery\Mock
	 */
	protected $translator;

	/**
	 * @var array
	 */
	protected $config;

	public function setUp()
	{
		$this->users = m::mock('Illuminate\Auth\UserProviderInterface');
		$this->reminders = m::mock('anlutro\Core\Auth\Reminders\DatabaseReminderRepository');
		$this->mailer = m::mock('Illuminate\Mail\Mailer');
		$this->translator = m::mock('Illuminate\Translation\Translator');
		$this->config = ['email-view' => 'view', 'queue-email' => false];
	}

	public function getBroker()
	{
		return new PasswordBroker($this->users, $this->reminders, $this->mailer, $this->translator, $this->config);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testRequestReset()
	{
		$user = $this->getMockUser();
		$this->reminders->shouldReceive('deleteUser')->with($user)->once();
		$this->reminders->shouldReceive('create')->with($user)->once();
		$this->setupMailExpectations();

		$this->assertTrue($this->getBroker()->requestReset($user));
	}

	public function testRequestResetQueue()
	{
		$user = $this->getMockUser();
		$this->reminders->shouldReceive('deleteUser')->with($user)->once();
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
		$user->shouldReceive('setPassword')->once()->with($newPassword);
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

		$this->setExpectedException('anlutro\Core\Auth\Reminders\ReminderException');
		$this->getBroker()->resetUser($user, $token, $newPassword);
	}
	
	protected function setupMailExpectations($method = 'send', $success = true)
	{
		$queue = ($method == 'send') ? false : true;
		$method = $queue ? 'queue' : 'send';
		$this->config['queue-email'] = $queue;
		$this->mailer->shouldReceive($method)->once();
		$this->mailer->shouldReceive('failures')->once()->andReturn($success ? [] : ['foo']);
	}

	protected function getMockUser($email = 'test@example.com')
	{
		$mock = m::mock('anlutro\Core\Auth\Reminders\RemindableInterface');
		$mock->shouldReceive('getReminderEmail')->andReturn($email);
		return $mock;
	}
}
