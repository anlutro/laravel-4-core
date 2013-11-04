<?php
/**
 * Laravel 4 Core - Base User class
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth;

use c\Auth\Activation\ActivatableInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\UserInterface;

class UserModel extends Model implements UserInterface, RemindableInterface, ActivatableInterface
{
	protected $table = 'users';

	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = Hash::make($value);
	}

	public function confirmPassword($password)
	{
		return Hash::check($password, $this->attributes['password']);
	}

	public function activate()
	{
		$this->attributes['activation_code'] = null;
		$this->attributes['is_active'] = true;
		return $this->save();
	}

	public function deactivate($activationCode = null)
	{
		if ($activationCode) {
			$this->attributes['activation_code'] = $activationCode;
		}
		
		$this->attributes['is_active'] = false;
		return $this->save();
	}

	public function scopeSearchFor($query, $search)
	{
		$searchable = ['username', 'email', 'name'];
		
		return $query->where(function($query) use ($searchable, $search) {
			foreach ($searchable as $field) {
				$query->orWhere($field, 'like', '%'.$search.'%');
			}
		});
	}

	public function scopeFilterUserType($query, $type)
	{
		return $query;

		return $query->where('user_type', '=', $type);
	}

	public function getDistinctUserTypes()
	{
		return $this->newQuery()
			->distinct()
			->lists('user_type');
	}

	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	public function getAuthPassword()
	{
		return $this->attributes['password'];
	}

	public function getReminderEmail()
	{
		return $this->attributes['email'];
	}
}
