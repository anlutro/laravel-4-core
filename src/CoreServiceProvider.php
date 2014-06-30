<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Carbon\Carbon;
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
		static::$resPath = __DIR__.'/../resources';

		$this->commands([
			'anlutro\Core\Auth\Console\CreateUserCommand',
			'anlutro\Core\Auth\Console\ChangePasswordCommand',
		]);

		$this->app->bind('anlutro\Core\Auth\UserManager');
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
		$this->registerRoutes('core');
		$this->addRouteFilters();

		$userModel = $this->app['config']->get('auth.model') ?: 'anlutro\Core\Auth\Users\UserModel';
		$this->app->bind('anlutro\Core\Auth\Users\UserModel', $userModel);
		$this->registerUserEvents($userModel);

		$this->registerAlertComposer();
		$this->registerSidebar();
		$this->registerMenus();
	}

	/**
	 * Register our config file location with the config repository.
	 *
	 * @return void
	 */
	protected function registerConfigFiles()
	{
		$this->app['config']->package('anlutro/l4-core', static::$resPath . '/config', 'c');
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
	 * Add route filters.
	 * 
	 * @return void
	 */
	protected function addRouteFilters()
	{
		$this->app['router']->filter('auth', 'anlutro\Core\Web\Filters\AuthFilter');
		$this->app['router']->filter('access', 'anlutro\Core\Web\Filters\AccessFilter');
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
		$userModel::creating(function(Model $user) {
			if (!isset($user->login_token)) {
				$user->setAttribute('login_token', Str::random(32));
			}
		});

		// set last_login on every successful login.
		$this->app['events']->listen('auth.login', function(Model $user) {
			$user->setAttribute('last_login', Carbon::now());
			$user->save();
		});
	}

	/**
	 * Register the alerts view composer.
	 *
	 * @return void
	 */
	protected function registerAlertComposer()
	{
		$this->app['view']->composer('c::alerts', 'anlutro\Core\Web\Composers\AlertsComposer');
	}

	/**
	 * Register the sidebar view creator.
	 *
	 * @return void
	 */
	protected function registerSidebar()
	{
		$this->app['view']->creator('c::sidebar', function(View $view) {
			$view->with('sidebar', new \Illuminate\Support\Collection);
		});
	}

	/**
	 * Register the default menu composer.
	 *
	 * @return void
	 */
	protected function registerMenus()
	{
		if (!$this->providerLoaded('anlutro\Menu\ServiceProvider')) return;

		/** @var \anlutro\Menu\Builder $menu */
		$menu = $this->app['anlutro\Menu\Builder'];
		/** @var \Illuminate\Translation\Translator $lang */
		$lang = $this->app['translator'];
		/** @var \Illuminate\Routing\UrlGenerator $url */
		$url = $this->app['url'];
		/** @var \anlutro\Core\Auth\Users\UserModel|null $user */
		$user = $this->app['auth']->user();

		$menu->createMenu('left', ['class' => 'nav navbar-nav navbar-left']);
		$menu->createMenu('right', ['class' => 'nav navbar-nav navbar-right']);

		if ($user !== null) {
			$subMenu = $menu->getMenu('right')->addSubmenu($user->name, ['id' => 'user', 'glyphicon' => 'user']);
			$subMenu->addItem(
				$lang->get('c::user.profile-title'),
				$url->action('anlutro\Core\Web\UserController@profile'),
				['id' => 'profile']
			);

			if ($user->hasAccess('admin')) {
				$subMenu->addItem(
					$lang->get('c::user.admin-userlist'),
					$url->action('anlutro\Core\Web\UserController@index'),
					['id' => 'userlist']
				);
				$subMenu->addItem(
					$lang->get('c::user.admin-newuser'),
					$url->action('anlutro\Core\Web\UserController@create'),
					['id' => 'add-user']
				);
			}

			$subMenu->addItem(
				$lang->get('c::auth.logout'),
				$url->action('anlutro\Core\Web\AuthController@logout'),
				['id' => 'log-out']
			);
		}

		$this->app['view']->composer('c::menu', 'anlutro\Core\Web\Composers\MenuViewComposer');
	}

	protected function providerLoaded($provider)
	{
		$providers = $this->app['config']->get('app.providers');
		return in_array($provider, $providers);
	}

	public static function getResPath()
	{
		return static::$resPath;
	}
}
