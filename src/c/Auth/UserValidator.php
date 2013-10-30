<?php
namespace c\Auth;

use anlutro\L4Base\Validator as BaseValidator;

class UserValidator extends BaseValidator
{
	protected $commonRules = [
		'username' => ['required', 'min:4', 'alpha_dash', 'unique:<table>,username,<key>'],
		'name' => ['required'],
		'email' => ['required', 'email', 'unique:<table>,email,<key>'],
		'phone' => ['numeric'],
		'password' => ['confirmed', 'min:6'],
	];

	public function __construct(UserModel $model)
	{
		$this->model = $model;
	}

	public function validCreate(array $attributes)
	{
		$rules = [
			'password' => ['required', 'confirmed', 'min:6'],
		];
		
		return $this->valid($rules, $attributes);
	}

	public function validUpdate(array $attributes)
	{
		$rules = [];

		return $this->valid([], $attributes);
	}

	public function validProfileUpdate(array $attributes)
	{
		$rules = [];

		return $this->valid($rules, $attributes);
	}
}
