<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c\Auth\Console;

/**
 * Command for sending a password reset email to a user.
 */
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
