<?php
/**
 * Laravel 4 Core - PasswordBroker replacement
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth;

use Illuminate\Auth\Reminders\ReminderServiceProvider as BaseProvider;

class ReminderServiceProvider extends BaseProvider
{
	protected function registerPasswordBroker()
	{
		$this->app['auth.reminder'] = $this->app->share(function($app) {

			// get the reminder repository
			$reminders = $app['auth.reminder.repository'];

			// get the user repository
			$users = $app['auth']->driver()->getProvider();

			// inject and construct
			$broker = new PasswordBroker($users, $reminders);
			
			return $broker;

		});
	}
}
