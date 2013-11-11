<?php
/**
 * Laravel 4 Core - Change user password command
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth\Console;

class ChangePasswordCommand extends AbstractUserManagementCommand
{
	protected $name = 'auth:changepass';
	protected $description = 'Change the password of an existing user.';

	public function fire()
	{
		$user = $this->getUser();

		do {
			$password = $this->ask('New password:');
			$confirm = $this->ask('Confirm new password:');
		} while ($password !== $confirm);

		$user->password = $password;
		$user->save();

		$this->info('Password updated!');
	}
}
