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
 * Command for activating a user via the command line.
 */
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
