<?php
/**
 * Laravel 4 Core - PasswordBroker replacement
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth\Reminders;

use Illuminate\Auth\Reminders\DatabaseReminderRepository as BaseRepository;
use Illuminate\Auth\Reminders\RemindableInterface;

class DatabaseReminderRepository extends BaseRepository
{
	/**
	 * Delete all tokens belonging to a user.
	 *
	 * @param  RemindableInterface $user
	 *
	 * @return void
	 */
	public function deleteUser(RemindableInterface $user)
	{
		$email = $user->getReminderEmail();

		$this->getTable()
			->where('email', '=', $email)
			->delete();
	}
}
