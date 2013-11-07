<?php
namespace c\View;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app['menubuilder'] = $this->app->share(function($app) {
			return new MenuBuilder;
		});
	}

	public function boot()
	{
		$this->app['view']->creator('c::sidebar', function($view) {
			$view->with('sidebar', array());
		});

		$this->app['view']->composer('partial.menu', function($view) {
			if ($this->app['auth']->check()) {
				$item = $this->app['menubuilder']->makeDropdown('user', $this->app['auth']->user()->name, 'user');
				$item->subMenu->addItem($this->app['menubuilder']->item('profile', $this->app['translator']->get('c::user.profile-title'), $this->app['url']->action('UserController@profile')));
				$item->subMenu->addItem($this->app['menubuilder']->item('logout', $this->app['translator']->get('c::auth.logout'), $this->app['url']->action('AuthController@logout')));
				$this->app['menubuilder']->add('right', $item);
			}
		});
	}

	public function provides()
	{
		return ['menubuilder'];
	}
}
