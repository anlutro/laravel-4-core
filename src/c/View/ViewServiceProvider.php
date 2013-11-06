<?php
namespace c\View;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app['menubuilder'] = $this->app->share(function($app) {
			return new MenuBuilder($app['html']);
		});
	}

	public function boot()
	{
		$this->app['view']->creator('c::sidebar', function($view) {
			$view->with('sidebar', array());
		});
	}

	public function provides()
	{
		return ['menubuilder'];
	}
}
