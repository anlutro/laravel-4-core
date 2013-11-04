<?php
namespace c\Auth\Console;

class SendPasswordReminderCommand extends AbstractUserManagementCommand
{
	protected $name = 'auth:sendreminder';
	protected $description = 'Send password reset instructions to a user.';

	public function fire()
	{
		$user = $this->getUser();

		$this->laravel['auth.reminder']->requestReset($user);

		$this->info('Email sent!');
	}
}
