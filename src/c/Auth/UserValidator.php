<?php
/**
 * Laravel 4 Core - User validator
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth;

class UserValidator extends \c\Validator
{
	protected $commonRules = [
		'username' => ['required', 'min:4', 'alpha_dash', 'unique:<table>,username,<key>'],
		'name' => ['required'],
		'email' => ['required', 'email', 'unique:<table>,email,<key>'],
		'phone' => ['numeric'],
		'password' => ['confirmed', 'min:6'],
	];

	protected $createRules = [
		'password' => ['required', 'confirmed', 'min:6']
	];

	public function __construct(UserModel $model)
	{
		$this->model = $model;
	}
}
