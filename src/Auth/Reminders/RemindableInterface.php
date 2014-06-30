<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Reminders;

interface RemindableInterface extends \Illuminate\Auth\Reminders\RemindableInterface
{
	/**
	 * Set a new password on the remindable object.
	 *
	 * @param  string $newPassword
	 *
	 * @return void
	 */
	public function setPassword($newPassword);
} 