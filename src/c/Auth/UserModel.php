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
		return Hash::check($password, $this->attributes['password']);
	}

	/**
	 * Add some simple search functionality.
	 * 
	 * $query->searchFor('admin')->get();
	 */
	public function scopeSearchFor($query, $search)
	{
		$searchable = ['username', 'email', 'name'];
		
		return $query->where(function($query) use ($searchable, $search) {
			foreach ($searchable as $field) {
				$query->orWhere($field, 'like', '%'.$search.'%');
			}
		});
	}

	/**
	 * Filter user types.
	 * 
	 * $query->filterUserType('superuser')->get();
	 */
	public function scopeFilterUserType($query, $type)
	{
		return $query->where('user_type', '=', $type);
	}

	/**
	 * Get a list of distinct user types.
	 *
	 * @return array
	 */
	public function getDistinctUserTypes()
	{
		return $this->newQuery()
			->distinct()
			->lists('user_type');
	}

	/**
	 * Make sure is_active returns the proper boolean.
	 */
	public function getIsActiveAttribute()
	{
		return $this->attributes['is_active'] === '1';
	}

	/********************
	 *  Authentication  *
	 ********************/

	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	public function getAuthPassword()
	{
		return $this->attributes['password'];
	}

	/********************
	 *    Reminders     *
	 ********************/

	public function getReminderEmail()
	{
		return $this->attributes['email'];
	}

	/********************
	 *    Activation    *
	 ********************/

	public function activate()
	{
		$this->attributes['is_active'] = true;
		return $this->save();
	}

	public function deactivate()
	{
		$this->attributes['is_active'] = false;
		return $this->save();
	}

	public function getActivationEmail()
	{
		return $this->attributes['email'];
	}
}
