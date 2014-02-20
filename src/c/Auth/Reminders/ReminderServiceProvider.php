<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c\Auth\Reminders;

use Illuminate\Auth\Reminders\ReminderServiceProvider as BaseProvider;

class ReminderServiceProvider extends BaseProvider
{
	use \c\RouteProviderTrait;
	
	protected $defer = false;
	protected $srcPath;

	protected function registerCommands()
	{
		parent::registerCommands();
		$this->commands('c\Auth\Console\SendPasswordReminderCommand');
	}

	public function register()
	{
		parent::register();
		
		$this->app->extend('c\Auth\UserManager', function($manager, $app) {
			$manager->setReminderService($app['auth.reminder']);
			return $manager;
		});
	}

	/**
	 * Register the reminder repository implementation.
	 *
	 * @return void
	 */
	protected function registerReminderRepository()
	{
		$this->app->bindShared('auth.reminder.repository', function($app)
		{
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
		$this->srcPath = __DIR__ . '/../../..';
		$this->registerRoutes('reminders');
	}
}
