<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Users;

use anlutro\LaravelValidation\Validator;

/**
 * Validator for the user repository.
 */
class UserValidator extends Validator
{
	protected function getCommonRules()
	{
		return [
			'username' => ['min:4', 'max:32', 'alpha_dash', $this->unique('username')],
			'email' => ['required', 'max:128', 'email', $this->unique('email')],
			'name' => ['required', 'max:128'],
			'phone' => ['regex:/^[\d ]+$/', 'max:16'],
			'password' => ['confirmed', 'min:6'],
		];
	}

	protected function getCreateRules()
	{
		return [
			'username' => ['required'],
			'password' => ['required'],
		];
	}

	public function getPasswordResetRules()
	{
		return [
			'password' => ['required', 'confirmed', 'min:6']
		];
	}

	public function validPasswordReset(array $attributes)
	{
		return $this->valid('passwordReset', $attributes, false);
	}

	protected function unique($column, $softDelete = true)
	{
		return 'unique:<table>,'.$column.',<key>'.($softDelete ? ',id,deleted_at,NULL':'');
	}
}
