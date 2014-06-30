<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Reminders;

use Illuminate\Auth\Reminders\ReminderServiceProvider as BaseProvider;
use anlutro\Core\CoreServiceProvider;

class ReminderServiceProvider extends BaseProvider
{
	use \anlutro\Core\RouteProviderTrait;
	
	protected $defer = false;
	protected static $resPath;

	public function register()
	{
		$this->registerReminderRepository();

		$this->registerPasswordBroker();

		$this->registerCommands();
	}

	protected function registerCommands()
	{
		parent::registerCommands();
		$this->commands('anlutro\Core\Auth\Console\SendPasswordReminderCommand');
	}

	/**
	 * Register the reminder repository implementation.
	 *
	 * @return void
	 */
	protected function registerReminderRepository()
	{
		$this->app->bindShared('auth.reminder.repository', function($app) {

			$connection = $app['db']->connection();
			$table = $app['config']['auth.reminder.table'];
			$key = $app['config']['app.key'];
			$expire = $app['config']->get('auth.reminder.expire', 60);

			return new DatabaseReminderRepository($connection, $table, $key, $expire);

		});
	}

	protected function registerPasswordBroker()
	{
		$this->app->bindShared('auth.reminder', function($app) {

			$reminders = $app['auth.reminder.repository'];
			$users = $app['auth']->driver()->getProvider();
			$mailer = $app['mailer'];
			$config = [
				'email-view' => $app['config']->get('auth.reminder.email') ?: 'c::auth.reset-email',
				'queue-email' => (bool) $app['config']->get('c::queue-reminder-mail', false),
			];

			return new PasswordBroker($users, $reminders, $mailer, $config);

		});
	}

	public function boot()
	{
		$this->app->extend('anlutro\Core\Auth\UserManager', function($manager, $app) {
			$manager->setReminderService($app['auth.reminder']);
			return $manager;
		});

		static::$resPath = CoreServiceProvider::getResPath();
		$this->registerRoutes('reminders');
	}
}
