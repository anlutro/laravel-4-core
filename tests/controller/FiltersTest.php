<?php

use Mockery as m;

class FiltersTest extends AppTestCase
{
	public function setUp()
	{
		parent::setUp();
		$this->app['router']->enableFilters();
		$this->app['router']->get('/auth-test', ['before' => 'auth', function() { return 'foo'; }]);
		$this->app['router']->get('/access-test', ['before' => 'auth|access:test', function() { return 'bar'; }]);
	}

	public function tearDown()
	{
		m::close();
	}

	/** @test */
	public function authFilterReturnsNullWhenLoggedIn()
	{
		$mockUser = m::mock('anlutro\Core\Auth\UserModel');
		$this->be($mockUser);
		$this->call('get', '/auth-test');
		$this->assertResponseOk();
	}

	/** @test */
	public function authFilterRedirectsWhenLoggedOut()
	{
		$this->call('get', '/auth-test');
		$this->assertRedirectedToAction('anlutro\Core\Web\AuthController@login');
	}

	/** @test */
	public function accessFilterReturnsNullWhenHasAccess()
	{
		$mockUser = m::mock('anlutro\Core\Auth\UserModel');
		$mockUser->shouldReceive('hasAccess')->once()->andReturn(true);
		$this->be($mockUser);
		$this->call('get', '/access-test');
		$this->assertResponseOk();
	}

	/** @test */
	public function accessFilterReturns403WhenAccessDenied()
	{
		$mockUser = m::mock('anlutro\Core\Auth\UserModel');
		$mockUser->shouldReceive('hasAccess')->once()->andReturn(false);
		$this->be($mockUser);
		$this->call('get', '/access-test');
		$this->assertResponseStatus(403);
	}
}
