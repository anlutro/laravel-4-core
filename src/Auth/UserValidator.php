<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth;

use anlutro\LaravelValidation\Validator;

/**
 * Validator for the user repository.
 */
class UserValidator extends Validator
{
	protected function getCommonRules()
	{
		return [
			'username' => ['required', 'min:4', 'alpha_dash', 'unique:<table>,username,<key>'],
			'name' => ['required'],
			'email' => ['required', 'email', 'unique:<table>,email,<key>'],
			'phone' => ['numeric'],
			'password' => ['confirmed', 'min:6'],
		];
	}

	protected function getCreateRules()
	{
		return [
			'password' => ['required'],
		];
	}

	public function validPasswordReset(array $attributes)
	{
		$rules = ['password' => ['required', 'confirmed', 'min:6']];
		return $this->valid($rules, $attributes, false);
	}
}
