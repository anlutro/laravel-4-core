<?php
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
			$broker = new PasswordBroker($reminders, $users);
			
			return $broker;

		});
	}
}
