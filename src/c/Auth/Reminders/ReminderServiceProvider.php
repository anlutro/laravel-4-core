<?php
/**
 * Laravel 4 Core - PasswordBroker replacement
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth\Reminders;

use Illuminate\Auth\Reminders\ReminderServiceProvider as BaseProvider;

class ReminderServiceProvider extends BaseProvider
{
	protected function registerCommands()
	{
		parent::registerCommands();
		$this->commands('c\Auth\Console\SendPasswordReminderCommand');
	}

	protected function registerPasswordBroker()
	{
		$this->app['auth.reminder'] = $this->app->share(function($app) {

			$reminders = $app['auth.reminder.repository'];
			$users = $app['auth']->driver()->getProvider();
			$mailer = $app['mailer'];
			$config = [
				'email-view' => $app['config']->get('auth.reminder.email'),
				'queue-email' => (bool) $app['config']->get('auth.reminder.queue'),
			];

			return new PasswordBroker($users, $reminders, $mailer, $config);

		});
	}

	public function boot()
	{
		$this->srcPath = __DIR__ . '/../../..';
		$this->requireRouteFile('reminders');
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
}
