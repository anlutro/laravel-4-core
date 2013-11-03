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
}
