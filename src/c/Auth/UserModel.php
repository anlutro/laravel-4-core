<?php
namespace c\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserModel extends Model implements UserInterface, RemindableInterface
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

	public function addSearchConstraint($query, $search)
	{
		$searchable = ['username', 'email', 'name'];
		
		$query->where(function($query) use ($searchable, $search) {
			foreach ($searchable as $field) {
				$query->orWhere($field, 'like', '%'.$search.'%');
			}
		});

		return $query;
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
