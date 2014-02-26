<?php

use Mockery as m;

class MenuViewTest extends AppTestCase
{
	public function testViewWhenLoggedOut()
	{
		$view = $this->makeView()->render();
		$this->assertContains('id="menu-left"', $view);
	}

	public function testViewWhenLoggedInAsNormalUser()
	{
		$this->mockUser(false);
		$view = $this->makeView()->render();
		$this->assertContains('id="menu-left"', $view);
		$this->assertContains('<ul class="nav navbar-nav pull-right" id="menu-right"', $view);
		$this->assertContains('<a href="#" class="dropdown-toggle" id="username" data-toggle="dropdown">', $view);
		$this->assertContains('id="profile">My profile</a>', $view);
		$this->assertContains('id="log-out">Log out</a>', $view);
		$this->assertNotContains('id="add-user">Add user</a>', $view);
		$this->assertNotContains('id="userlist">List of users</a>', $view);
	}

	public function testViewWhenLoggedInAsAdmin()
	{
		$this->mockUser(true);
		$view = $this->makeView()->render();
		$this->assertContains('id="menu-left"', $view);
		$this->assertContains('<ul class="nav navbar-nav pull-right" id="menu-right"', $view);
		$this->assertContains('<a href="#" class="dropdown-toggle" id="username" data-toggle="dropdown">', $view);
		$this->assertContains('id="profile">My profile</a>', $view);
		$this->assertContains('id="log-out">Log out</a>', $view);
		$this->assertContains('id="add-user">Add user</a>', $view);
		$this->assertContains('id="userlist">List of users</a>', $view);
	}

	public function mockUser($isAdmin = false)
	{
		$user = m::mock('c\Auth\UserModel')->makePartial();
		$user->name = 'UserName';
		$user->shouldReceive('hasAccess')->with('admin')->once()->andReturn($isAdmin);
		$this->be($user);
		return $user;
	}

	protected function makeView()
	{
		$this->app->register('anlutro\Menu\ServiceProvider');
		return $this->app['view']->make('c::menu');
	}
}
