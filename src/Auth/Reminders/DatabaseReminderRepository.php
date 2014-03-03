<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c\Auth\Reminders;

use Illuminate\Auth\Reminders\DatabaseReminderRepository as BaseRepository;
use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * Repository for database password reminders.
 */
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