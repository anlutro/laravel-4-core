<?php
namespace c\Auth\Console;

class ActivateUserCommand extends AbstractUserManagementCommand
{
	protected $name = 'auth:activate';
	protected $description = 'Activate an existing user.';

	public function fire()
	{
		$user = $this->getUser();

		$user->activate();

		$this->info('User activated!');
	}
}
