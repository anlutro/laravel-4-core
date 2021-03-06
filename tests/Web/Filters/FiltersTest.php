<?php
namespace anlutro\Core\Tests\Web\Filters;

use Mockery as m;
use anlutro\Core\Tests\AppTestCase;

/** @medium */
class FiltersTest extends AppTestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->app['router']->enableFilters();
		$this->app['router']->get('/auth-test', ['before' => 'auth', function() { return 'OK'; }]);
		$this->app['router']->get('/guest-test', ['before' => 'guest', function() { return 'OK'; }]);
		$this->app['router']->get('/access-test', ['before' => 'auth|access:test', function() { return 'OK'; }]);
		$this->app['router']->get('/more-access-test', ['before' => 'auth|access:test1,test2', function() { return 'OK'; }]);
	}

	public function tearDown()
	{
		m::close();
	}

	/** @test */
	public function authFilterReturnsNullWhenLoggedIn()
	{
		$mockUser = $this->mockUser();
		$this->be($mockUser);
		$this->call('get', '/auth-test');
		$this->assertResponseOk();
	}

	/** @test */
	public function guestFilterReturnsNullWhenLoggedOut()
	{
		$this->call('get', '/guest-test');
		$this->assertResponseOk();
	}

	/** @test */
	public function authFilterRedirectsWhenLoggedOut()
	{
		$this->call('get', '/auth-test');
		$this->assertRedirectedToAction('anlutro\Core\Web\AuthController@login');
	}

	/** @test */
	public function guestFilterRedirectsWhenLoggedIn()
	{
		$mockUser = $this->mockUser();
		$this->be($mockUser);
		$this->call('get', '/guest-test');
		$this->assertRedirectedTo('/');
	}

	/** @test */
	public function accessFilterReturnsNullWhenHasAccess()
	{
		$mockUser = $this->mockUser();
		$mockUser->shouldReceive('hasAccess')->once()->with('test')->andReturn(true);
		$this->be($mockUser);
		$this->call('get', '/access-test');
		$this->assertResponseOk();
	}

	/** @test */
	public function accessFilterReturns403WhenAccessDenied()
	{
		$mockUser = $this->mockUser();
		$mockUser->shouldReceive('hasAccess')->once()->with('test')->andReturn(false);
		$this->be($mockUser);
		$this->call('get', '/access-test');
		$this->assertResponseStatus(403);
	}

	/** @test */
	public function accessFilterChecksForAllArgs()
	{
		$mockUser = $this->mockUser();
		$mockUser->shouldReceive('hasAccess')->with('test1')->once()->andReturn(true);
		$mockUser->shouldReceive('hasAccess')->with('test2')->once()->andReturn(false);
		$this->be($mockUser);
		$this->call('get', '/more-access-test');
		$this->assertResponseStatus(403);
	}

	/** @test */
	public function accessFilterStopsAfterFirstFalse()
	{
		$mockUser = $this->mockUser();
		$mockUser->shouldReceive('hasAccess')->with('test1')->once()->andReturn(false);
		$mockUser->shouldReceive('hasAccess')->with('test2')->never();
		$this->be($mockUser);
		$this->call('get', '/more-access-test');
		$this->assertResponseStatus(403);
	}

	/** @test */
	public function accessFilterReturnsNullWhenAllAccessAllowed()
	{
		$mockUser = $this->mockUser();
		$mockUser->shouldReceive('hasAccess')->with('test1')->once()->andReturn(true);
		$mockUser->shouldReceive('hasAccess')->with('test2')->once()->andReturn(true);
		$this->be($mockUser);
		$this->call('get', '/more-access-test');
		$this->assertResponseOk();
	}

	protected function mockUser()
	{
		$mock = m::mock('anlutro\Core\Auth\Users\UserModel')->makePartial();
		// menu view composer call
		$mock->shouldReceive('hasAccess')->with('admin')->andReturn(false)->byDefault();
		return $mock;
	}
}
