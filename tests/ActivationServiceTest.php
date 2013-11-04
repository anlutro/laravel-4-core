<?php
use Mockery as m;
use c\Auth\Activation\ActivationService;

class ActivationServiceTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->codes = m::mock('c\Auth\Activation\ActivationCodeRepositoryInterface');
		$this->users = m::mock('Illuminate\Auth\UserProviderInterface');
		$this->mailer = m::mock('Illuminate\Mail\Mailer');
		$this->activation = new ActivationService($this->codes, $this->users, $this->mailer, 'hashkey');
	}

	public function tearDown()
	{
		m::close();
	}

	public function testGenerate()
	{
		$user = $this->getMockUser();
		$this->codes->shouldReceive('create')->with($user, m::type('string'));
		$user->shouldReceive('getActivationEmail')->andReturn('test@example.com');
		$this->mailer->shouldReceive('send')->once()->andReturn(true);

		$this->assertTrue($this->activation->generate($user));
	}

	public function testActivate()
	{
		$user = $this->getMockUser();
		$code = new StdClass; $code->code = 'bar'; $code->email = 'test@example.com';
		$this->codes->shouldReceive('retrieveByCode')->with('foo')->andReturn($code);
		$this->users->shouldReceive('retrieveByCredentials')->with(['email' => 'test@example.com'])->andReturn($user);
		$user->shouldReceive('activate')->once()->andReturn(true);
		$this->codes->shouldReceive('delete')->once()->with('bar')->andReturn(true);

		$this->assertTrue($this->activation->activate('foo'));
	}

	protected function getMockUser()
	{
		return m::mock('c\Auth\Activation\ActivatableInterface');
	}
}
