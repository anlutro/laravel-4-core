<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core;

use anlutro\Core\Auth\Users\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class CoreServiceProvider extends ServiceProvider
{
	use \anlutro\Core\RouteProviderTrait;

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
	 * The name of the user model class.
	 *
	 * @var string
	 */
	protected $userModel;

	/**
	 * The path to the packages's resources directory.
	 *
	 * @var string
	 */
	protected static $resPath;

	/**
	 * Register IoC bindings.
	 *
	 * @return void
	 */
	public function register()
	{
		static::$resPath = dirname(__DIR__).'/resources';

		$this->commands([
			'anlutro\Core\Console\PublishCommand',
			'anlutro\Core\Auth\Console\CreateUserCommand',
			'anlutro\Core\Auth\Console\ChangePasswordCommand',
		]);

		$this->app->bind('anlutro\Core\Auth\UserManager');

		$this->app->bind('Illuminate\Database\Connection', function($app) {
			return $app->make('db')->connection();
		});

		$this->userModel = $this->app['config']->get('auth.model') ?: 'anlutro\Core\Auth\Users\UserModel';
		$this->app->bind('anlutro\Core\Auth\Users\UserModel', $this->userModel);

		$this->app->bindShared('anlutro\Core\Html\ScriptManager', function($app) {
			return new Html\ScriptManager($app['config']->get('app.debug'));
		});
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

		$this->registerConfigFiles();
		$this->registerLangFiles();
		$this->registerViewFiles();

		$this->registerAuthDriver();
		$this->registerRouteFilters();
		$this->registerRoutes('core');
		if ($this->app['config']->get('c::support-email')) {
			$this->registerRoutes('support');
		}
		$this->registerViewEvents();
		$this->registerUserEvents($this->userModel);

		$this->app->booted(function() {
			$this->registerErrorHandlers();
		});
	}

	/**
	 * Register our config file location with the config repository.
	 *
	 * @return void
	 */
	protected function registerConfigFiles()
	{
		$this->app['config']->package($this->package, static::$resPath . '/config', $this->namespace);
	}

	/**
	 * Register our language file location with the translator.
	 *
	 * @return void
	 */
	protected function registerLangFiles()
	{
		$this->app['translator']->addNamespace($this->namespace, static::$resPath . '/lang');
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

		$this->app['view']->addNamespace($this->namespace, static::$resPath . '/views');
	}

	/**
	 * Register the custom "eloquent-exceptions" driver that throws descriptive
	 * exceptions when authentication fails.
	 *
	 * @return void
	 */
	protected function registerAuthDriver()
	{
		$this->app['auth']->extend('eloquent-exceptions', function() {
			$model = $this->app['config']['auth.model'];
			return new Auth\Laravel\EloquentUserProvider($this->app['hash'], $model);
		});
	}

	/**
	 * Add route filters.
	 * 
	 * @return void
	 */
	protected function registerRouteFilters()
	{
		$this->app['router']->filter('guest', 'anlutro\Core\Web\Filters\GuestFilter');
		$this->app['router']->filter('auth', 'anlutro\Core\Web\Filters\AuthFilter');
		$this->app['router']->filter('access', 'anlutro\Core\Web\Filters\AccessFilter');
		$this->app->bind('anlutro\Core\Web\Filters\CsrfFilter', function($app) {
			$regenerate = $app['config']->get('c::regenerate-csrf');
			return new Web\Filters\CsrfFilter($app['session'], $regenerate);
		});
		$this->app['router']->filter('csrf', 'anlutro\Core\Web\Filters\CsrfFilter');
		$this->app['router']->before(function(Request $request) {
			if ($request->ajax() || $request->isJson() || $request->wantsJson()) {
				$this->app->bind('anlutro\Core\Web\AuthController', 'anlutro\Core\Web\ApiAuthController');
				$this->app->bind('anlutro\Core\Web\UserController', 'anlutro\Core\Web\ApiUserController');
			}
		});
	}

	/**
	 * Register the user model events.
	 *
	 * @param  string|Model $userModel
	 *
	 * @return void
	 */
	protected function registerUserEvents($userModel)
	{
		// set a random login token on creation.
		$userModel::creating(function(UserModel $user) {
			$user->generateLoginToken();
		});

		// set last_login on every successful login.
		$this->app['events']->listen('auth.login', function(UserModel $user) {
			$user->updateLastLogin();
		});
	}

	/**
	 * Register view events (composers and creators).
	 *
	 * @return void
	 */
	protected function registerViewEvents()
	{
		$view = $this->app['view'];

		$view->creator('c::layout.main-generic', 'anlutro\Core\Web\Composers\GenericLayoutCreator');

		$view->creator(['c::layout.main-sidebar', 'c::layout.main-nosidebar'], 'anlutro\Core\Web\Composers\MainLayoutCreator');

		$view->creator('c::layout.main-sidebar', 'anlutro\Core\Web\Composers\SidebarLayoutCreator');

		$view->creator('c::alerts', 'anlutro\Core\Web\Composers\AlertsViewCreator');

		$view->creator('c::sidebar', function(View $view) {
			$view->with('sidebar', new \Illuminate\Support\Collection);
		});

		$view->creator('c::menu', 'anlutro\Core\Web\Composers\MenuViewCreator');

		$view->composer('c::menu', function() {
			$this->registerMenus();
		});
	}

	/**
	 * Add menu items.
	 *
	 * @return void
	 */
	protected function registerMenus()
	{
		/** @var \anlutro\Menu\Builder $menu */
		$menu = $this->app['anlutro\Menu\Builder'];
		/** @var \Illuminate\Translation\Translator $lang */
		$lang = $this->app['translator'];
		/** @var \Illuminate\Routing\UrlGenerator $url */
		$url = $this->app['url'];
		/** @var \anlutro\Core\Auth\Users\UserModel|null $user */
		$user = $this->app['auth']->user();

		if ($user !== null) {
			$subMenu = $menu->getMenu('right')
				->addSubmenu($user->name, ['id' => 'user', 'glyphicon' => 'user'], 99);
			$subMenu->addItem(
				$lang->get('c::user.profile-title'),
				$url->route('c::profile'),
				['id' => 'profile']
			);

			$subMenu->addItem(
				$lang->get('c::auth.logout'),
				$url->route('c::logout'),
				['id' => 'log-out']
			);
		} else {
			$menu->getMenu('right')->addItem(
				$lang->get('c::auth.login-title'),
				$url->route('c::login'),
				['id' => 'log-in', 'glyph' => 'log-in'],
				99
			);
		}
	}

	/**
	 * Register the default/fallback error handlers.
	 *
	 * @return void
	 */
	protected function registerErrorHandlers()
	{
		if (!$this->providerLoaded('anlutro\L4SmartErrors\L4SmartErrorsServiceProvider')) return;

		(new ErrorHandler($this->app))->register();
	}

	/**
	 * Check if a service provider is present in the application.
	 *
	 * @param  string $provider
	 *
	 * @return boolean
	 */
	protected function providerLoaded($provider)
	{
		$providers = $this->app['config']->get('app.providers')
			+ array_keys($this->app->getLoadedProviders());

		return in_array($provider, $providers);
	}

	/**
	 * Get the path to the core resource files.
	 *
	 * @return string
	 */
	public static function getResPath()
	{
		return static::$resPath;
	}
}
