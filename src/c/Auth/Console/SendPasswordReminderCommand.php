<?php
/**
 * Laravel 4 Core - Send password reminder command
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth\Console;

class SendPasswordReminderCommand extends AbstractUserManagementCommand
{
	protected $name = 'auth:send-reminder';
	protected $description = 'Send password reset instructions to a user.';

	public function fire()
	{
		$user = $this->getUser();

		$this->laravel['auth.reminder']->requestReset($user);

		$this->info('Email sent!');
	}
}
