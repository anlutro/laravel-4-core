<?php
namespace c\Auth\Console;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SendPasswordReminderCommand extends AbstractUserManagementCommand
{
	protected $name = 'user:sendreminder';
	protected $description = 'Send password reset instructions to a user.';

	public function fire()
	{
		$user = $this->getUser();

		$this->laravel['auth.reminder']->requestReset($user);

		$this->info('Email sent!');
	}
}
