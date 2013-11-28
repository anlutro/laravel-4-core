<?php
/**
 * Laravel 4 Core - View class service provider
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

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
				$username = $this->app['auth']->user()->name;
				$item = $this->app['menubuilder']->makeDropdown('user', $username, 'user');

				$subItem = $this->app['menubuilder']->item('profile');
				$subItem->title = $this->app['translator']->get('c::user.profile-title');
				$subItem->url = $this->app['url']->action('UserController@profile');
				$item->subMenu->addItem($subItem);

				$subItem = $this->app['menubuilder']->item('logout');
				$subItem->title = $this->app['translator']->get('c::auth.logout');
				$subItem->url = $this->app['url']->action('AuthController@logout');
				$item->subMenu->addItem($subItem);

				if ($this->app['auth']->user()->hasAccess('admin')) {
					$subItem = $this->app['menubuilder']->item('users');
					$subItem->title = $this->app['translator']->get('c::user.admin-userlist');
					$subItem->url = $this->app['url']->action('UserController@index');
					$item->subMenu->addItem($subItem);

					$subItem = $this->app['menubuilder']->item('newuser');
					$subItem->title = $this->app['translator']->get('c::user.admin-newuser');
					$subItem->url = $this->app['url']->action('UserController@create');
					$item->subMenu->addItem($subItem);
				}
				
				$this->app['menubuilder']->add('right', $item);
			}
		}, -99); // low priority to force our menu to the right
	}

	public function provides()
	{
		return ['menubuilder'];
	}
}
