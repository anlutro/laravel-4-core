<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c;

use c\Auth\PasswordBroker;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class CoreServiceProvider extends ServiceProvider
{
	use \c\RouteProviderTrait;

	/**
	 * Whether or not the service provider should be deferred/lazy loaded.
	 *
	 * @var boolean
	 */
	protected $defer = false;

	/**
	 * The name of the package.
	 *
	 * @var string
	 */
	protected $package;

	/**
	 * The namespace that should be registered with the config, translator,
	 * view finder and so on.
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * The path to the packages's src directory.
	 *
	 * @var string
	 */
	protected $srcPath;

	/**
	 * Register IoC bindings.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->commands([
			'c\Auth\Console\CreateUserCommand',
			'c\Auth\Console\ChangePasswordCommand',
		]);
	}

	/**
	 * Run on application boot.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package = 'anlutro/l4-core';
		$this->namespace = 'c';
		$this->srcPath = __DIR__ . '/..';

		$this->registerConfigFiles();
		$this->registerLangFiles();
		$this->registerViewFiles();
		$this->registerRoutes('core');
		$this->addRouteFilters();

		$userModel = $this->app['config']->get('auth.model', 'c\Auth\UserModel');
		$this->app->bind('c\Auth\UserModel', $userModel);
		$this->registerUserEvents($userModel);
	}

	/**
	 * Register our config file location with the config repository.
	 *
	 * @return void
	 */
	protected function registerConfigFiles()
	{
		$this->app['config']->package('anlutro/l4-core', $this->srcPath . '/config', 'c');
	}

	/**
	 * Register our language file location with the translator.
	 *
	 * @return void
	 */
	protected function registerLangFiles()
	{
		$this->app['translator']->addNamespace($this->namespace, $this->srcPath . '/lang');
	}

	/**
	 * Register our views with the view file loader.
	 *
	 * @return void
	 */
	protected function registerViewFiles()
	{
		$appView = $this->getAppViewPath($this->package, $this->namespace);
		
		if ($this->app['files']->isDirectory($appView)) {
			$this->app['view']->addNamespace($this->namespace, $appView);
		}

		$this->app['view']->addNamespace($this->namespace, $this->srcPath . '/views');
	}

	/**
	 * Add route filters.
	 * 
	 * @return void
	 */
	protected function addRouteFilters()
	{
		$this->registerAuthFilter();
		$this->registerAccessFilter();
	}

	/**
	 * Register our custom auth filter.
	 *
	 * @return void
	 */
	protected function registerAuthFilter()
	{
		$this->app['router']->filter('auth', function($route, $request) {
			if ($this->app['auth']->guest()) {
				$message = $this->app['translator']->get('c::auth.login-required');

				if ($request->ajax() || $request->isJson() || $request->wantsJson()) {
					return Response::json(['error' => $message], 403);
				} else {
					return $this->app['redirect']->action('AuthController@login')
						->withErrors($message);
				}
			}
		});
	}

	/**
	 * Register the access level filter.
	 *
	 * @return void
	 */
	protected function registerAccessFilter()
	{
		$this->app['router']->filter('access', function($route, $request, $params) {
			if (!$user = $this->app['auth']->user()) {
				throw new \RuntimeException('auth filter must precede access filter');
			}

			foreach ((array) $params as $access) {
				if (!$user->hasAccess($access)) {
					$message = $this->app['translator']->get('c::auth.access-denied');

					if ($request->ajax() || $request->isJson() || $request->wantsJson()) {
						return Response::json(['error' => $message], 403);
					} else {
						return $this->app['redirect']->to('/')
							->withErrors($message);
					}
				}
			}
		});
	}

	/**
	 * Register the user model events.
	 *
	 * @param  string $userModel
	 *
	 * @return void
	 */
	protected function registerUserEvents($userModel)
	{
		// set a random login token on creation.
		$userModel::creating(function($user) {
			if (!isset($user->login_token)) {
				$user->setAttribute('login_token', Str::random(32));
			}
		});

		// set last_login on every successful login.
		$this->app['events']->listen('auth.login', function($user) {
			$user->setAttribute('last_login', Carbon::now());
			$user->save();
		});
	}

}
