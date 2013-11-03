<?php
namespace c\Auth\Console;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ActivateUserCommand extends AbstractUserManagementCommand
{
	protected $name = 'user:activate';
	protected $description = 'Activate an existing user.';

	public function fire()
	{
		$user = $this->getUser();

		$user->activate();

		$this->info('User activated!');
	}
}
