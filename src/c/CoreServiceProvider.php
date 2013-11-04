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
use Illuminate\Support\Str;
use Carbon\Carbon;

class CoreServiceProvider extends ServiceProvider
{

	protected $package;
	protected $namespace;
	protected $srcPath;

	public function register()
	{
		$this->commands([
			'c\Auth\Console\CreateUserCommand',
			'c\Auth\Console\ChangePasswordCommand',
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
		$this->requireRouteFile('core');

		$userModel = $this->app['config']->get('auth.model', 'c\Auth\UserModel');
		$this->app->bind('c\Auth\UserModel', $userModel);
		$this->registerUserEvents($userModel);

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

	protected function requireRouteFile($file)
	{
		$locale = $this->app['translator']->getLocale();
		$path = $this->srcPath . '/routes/' . $locale;

		if (!is_dir($path)) {
			$path = $this->srcPath . '/routes/en';
		}

		require $path . '/' . $file . '.php';
	}

	protected function addSidebarFunctionality()
	{
		$this->app['view']->creator('c::sidebar', function($view) {
			$view->with('sidebar', array());
		});
	}

	protected function registerUserEvents($userModel)
	{
		// set a random login token on creation.
		$userModel::creating(function($user) {
			$user->setAttribute('login_token', Str::random(32));
		});

		// set last_login on every successful login.
		$this->app['events']->listen('auth.login', function($user) {
			$user->setAttribute('last_login', Carbon::now());
			$user->save();
		});
	}

}
