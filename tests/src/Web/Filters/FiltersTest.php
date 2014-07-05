<?php
namespace anlutro\Core\Tests\Web\Filters;

use Mockery as m;
use anlutro\Core\Tests\AppTestCase;

class FiltersTest extends AppTestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->app['router']->enableFilters();
		$this->app['router']->get('/auth-test', ['before' => 'auth', function() { return 'OK'; }]);
		$this->app['router']->get('/guest-test', ['before' => 'guest', function() { return 'OK'; }]);
		$this->app['router']->get('/access-test', ['before' => 'auth|access:test', function() { return 'OK'; }]);
	}

	public function tearDown()
	{
		m::close();
	}

	/** @test */
	public function authFilterReturnsNullWhenLoggedIn()
	{
		$mockUser = m::mock('anlutro\Core\Auth\Users\UserModel');
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
		$mockUser = m::mock('anlutro\Core\Auth\Users\UserModel');
		$this->be($mockUser);
		$this->call('get', '/guest-test');
		$this->assertRedirectedTo('/');
	}

	/** @test */
	public function accessFilterReturnsNullWhenHasAccess()
	{
		$mockUser = m::mock('anlutro\Core\Auth\Users\UserModel');
		$mockUser->shouldReceive('hasAccess')->once()->andReturn(true);
		$this->be($mockUser);
		$this->call('get', '/access-test');
		$this->assertResponseOk();
	}

	/** @test */
	public function accessFilterReturns403WhenAccessDenied()
	{
		$mockUser = m::mock('anlutro\Core\Auth\Users\UserModel');
		$mockUser->shouldReceive('hasAccess')->once()->andReturn(false);
		$this->be($mockUser);
		$this->call('get', '/access-test');
		$this->assertResponseStatus(403);
	}
}
