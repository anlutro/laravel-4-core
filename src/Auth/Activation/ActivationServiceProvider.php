<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Activation;

use anlutro\Core\Auth\UserManager;
use Illuminate\Support\ServiceProvider;
use anlutro\Core\CoreServiceProvider;

class ActivationServiceProvider extends ServiceProvider
{
	use \anlutro\Core\RouteProviderTrait;
	
	protected $defer = false;
	protected static $resPath;

	public function register()
	{
		$this->commands('anlutro\Core\Auth\Console\ActivateUserCommand');
		
		$this->app->bindShared('auth.activation.repository', function($app) {
			return new DatabaseActivationCodeRepository($app['db']->connection(), 'user_activation');
		});

		$this->app->bindShared('auth.activation', function($app) {

			$codes = $app['auth.activation.repository'];
			$users = $app['auth']->driver()->getProvider();
			$mailer = $app['mailer'];
			$translator = $app['translator'];
			$hashKey = $app['config']->get('app.key');
			$queue = $app['config']->get('auth.reminders.queue', false);

			return new ActivationService($codes, $users, $mailer, $translator, $hashKey, $queue);

		});
	}

	public function boot()
	{
		$this->app->extend('anlutro\Core\Auth\UserManager', function(UserManager $manager, $app) {
			$manager->setActivationService($app['auth.activation']);
			return $manager;
		});

		static::$resPath = CoreServiceProvider::getResPath();
		$this->registerRoutes('activation');
	}
}
