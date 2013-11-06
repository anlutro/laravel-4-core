<?php
namespace c\View;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->app->bindShared('menubuilder', function($app) {
			return new MenuBuilder($app['html']);
		});
	}

	public function boot()
	{
		$this->app['view']->creator('c::sidebar', function($view) {
			$view->with('sidebar', array());
		});
	}
}
