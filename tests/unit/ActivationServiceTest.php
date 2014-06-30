<?php
namespace anlutro\Core\Tests;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use anlutro\Core\Auth\Activation\ActivationService;

class ActivationServiceTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var \Mockery\Mock
	 */
	protected $codes;

	/**
	 * @var \Mockery\Mock
	 */
	protected $users;

	/**
	 * @var \Mockery\Mock
	 */
	protected $translator;

	/**
	 * @var \Mockery\Mock
	 */
	protected $mailer;

	/**
	 * @var ActivationService
	 */
	protected $activation;

	public function setUp()
	{
		$this->codes = m::mock('anlutro\Core\Auth\Activation\ActivationCodeRepositoryInterface');
		$this->users = m::mock('Illuminate\Auth\UserProviderInterface');
		$this->translator = m::mock('Illuminate\Translation\Translator');
		$this->mailer = m::mock('Illuminate\Mail\Mailer');
		$this->activation = new ActivationService($this->codes, $this->users, $this->mailer, $this->translator, 'hashkey', false);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testGenerate()
	{
		$user = $this->getMockUser();
		$user->shouldReceive('deactivate')->once();
		$this->codes->shouldReceive('deleteUser')->with($user);
		$this->codes->shouldReceive('create')->with($user, m::type('string'));
		$user->shouldReceive('getActivationEmail')->andReturn('test@example.com');
		$this->mailer->shouldReceive('send')->once()->andReturn(true);
		$this->mailer->shouldReceive('failures')->once()->andReturn([]);

		$this->assertTrue($this->activation->generate($user));
	}

	public function testActivate()
	{
		$user = $this->getMockUser();
		$this->codes->shouldReceive('retrieveEmailByCode')->with('foo')->andReturn('test@example.com');
		$this->users->shouldReceive('retrieveByCredentials')->with(['email' => 'test@example.com'])->andReturn($user);
		$user->shouldReceive('activate')->once()->andReturn(true);
		$this->codes->shouldReceive('delete')->once()->with('foo')->andReturn(true);

		$this->activation->activate('foo');
	}

	protected function getMockUser()
	{
		return m::mock('anlutro\Core\Auth\Activation\ActivatableInterface');
	}
}
