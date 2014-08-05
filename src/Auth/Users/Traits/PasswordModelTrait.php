<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Users\Traits;

use Illuminate\Support\Facades\Hash;

trait PasswordModelTrait
{
	/**
	 * Hash the password automatically when setting it.
	 */
	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = Hash::make($value);
	}

	/**
	 * Check if a password is correct.
	 */
	public function confirmPassword($password)
	{
		return Hash::check($password, $this->getAttributeFromArray('password'));
	}

	/**
	 * Check if the user's password needs rehashing.
	 */
	public function rehashPassword($password)
	{
		if (!Hash::needsRehash($this->getAttributeFromArray('password'))) return;

		if (!$this->confirmPassword($password)) return;

		$this->setPasswordAttribute($password);

		return $this->save();
	}
}
