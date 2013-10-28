<?php
namespace c;

use c\Auth\PasswordBroker;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{

	public function register()
	{
		// ...
	}

	public function boot()
	{
		$package = 'anlutro/l4-core';
		$namespace = 'c';
		$srcPath = __DIR__ . '/..';

		$this->app['translator']->addNamespace($namespace, $srcPath . '/lang');

		$appView = $this->getAppViewPath($package, $namespace);
		
		if ($this->app['files']->isDirectory($appView)) {
			$this->app['view']->addNamespace($namespace, $appView);
		}

		$this->app['view']->addNamespace($namespace, $srcPath . '/views');
	}

}
