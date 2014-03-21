<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Console;

/**
 * Command for changing a user's password via the console.
 */
class ChangePasswordCommand extends AbstractUserManagementCommand
{
	protected $name = 'auth:changepass';
	protected $description = 'Change the password of an existing user.';

	public function fire()
	{
		$user = $this->getUser();

		do {
			$password = $this->secret('New password:');
			$confirm = $this->secret('Confirm new password:');
		} while ($password !== $confirm);

		$user->password = $password;
		$user->save();

		$this->info('Password updated!');
	}
}
