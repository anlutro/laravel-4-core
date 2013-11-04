<?php
/**
 * Laravel 4 Core - Service provider
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c;

use c\Auth\PasswordBroker;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{

	protected $package;
	protected $namespace;
	protected $srcPath;

	public function register()
	{
		$this->commands([
			'c\Auth\Console\CreateUserCommand',
			'c\Auth\Console\ActivateUserCommand',
			'c\Auth\Console\ChangePasswordCommand',
			'c\Auth\Console\SendPasswordReminderCommand',
		]);
	}

	public function boot()
	{
		$this->package = 'anlutro/l4-core';
		$this->namespace = 'c';
		$this->srcPath = __DIR__ . '/..';

		$this->registerLangFiles();
		$this->registerViewFiles();
		$this->addSidebarFunctionality();
		$this->requireRouteFiles();

		$this->app->bind('c\Auth\UserModel', $this->app['config']->get('auth.model', 'c\Auth\UserModel'));
	}

	protected function registerLangFiles()
	{
		$this->app['translator']->addNamespace($this->namespace, $this->srcPath . '/lang');
	}

	protected function registerViewFiles()
	{
		$appView = $this->getAppViewPath($this->package, $this->namespace);
		
		if ($this->app['files']->isDirectory($appView)) {
			$this->app['view']->addNamespace($this->namespace, $appView);
		}

		$this->app['view']->addNamespace($this->namespace, $this->srcPath . '/views');
	}

	protected function requireRouteFiles()
	{
		require $this->srcPath . '/routes-' . $this->app['translator']->getLocale() . '.php';
	}

	protected function addSidebarFunctionality()
	{
		$this->app['view']->creator('c::sidebar', function($view) {
			$view->with('sidebar', array());
		});
	}

}
