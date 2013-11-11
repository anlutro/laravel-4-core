<?php
/**
 * Laravel 4 Core - User activation command
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

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
