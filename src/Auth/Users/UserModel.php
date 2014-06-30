<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Users;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

use anlutro\Core\Auth\Activation\ActivatableInterface;
use anlutro\Core\Eloquent\Model;

/**
 * User model.
 */
class UserModel extends Model implements UserInterface, RemindableInterface, ActivatableInterface
{
	use SoftDeletingTrait;

	/**
	 * The database table the model queries from.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are fillable by mass assignment.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'phone', 'password'];

	/**
	 * Attributes that are hidden from toArray (and by extension, from toJson)
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'login_token', 'remember_token'];

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

		$this->password = $password;
		return $this->save();
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
	 * $query->whereUserType('superuser')->get();
	 */
	public function scopeWhereUserType($query, $level)
	{
		return $query->where('user_level', '=', $this->getUserLevelValue($level));
	}

	/**
	 * Setter for is_active.
	 */
	public function setIsActiveAttribute($value)
	{
		$this->attributes['is_active'] = $value ? '1' : '0';
	}

	/**
	 * Getter for is_active.
	 */
	public function getIsActiveAttribute($value)
	{
		return $value === '1';
	}

	/********************
	 *  Access levels   *
	 ********************/

	protected static $accessLevels = [
		'user' => 1,
		'mod' => 10,
		'admin' => 100,
		'superadmin' => 255,
	];

	/**
	 * Get the list of access levels.
	 *
	 * @return array
	 */
	public function getAccessLevels()
	{
		return static::$accessLevels;
	}

	/**
	 * Check if a user has access to a certain level of actions.
	 *
	 * @param  string  $access
	 *
	 * @return boolean
	 */
	public function hasAccess($access)
	{
		if ($access === '*') {
			$access = 'superadmin';
		}

		return $this->user_level >= $this->getUserLevelValue($access);
	}

	/**
	 * Make sure the user_level attribute is cast to an int.
	 *
	 * @param  string $value
	 *
	 * @return int
	 */
	public function getUserLevelAttribute($value)
	{
		return (int) $value;
	}

	/**
	 * Set the user_level attribute.
	 *
	 * @param string|int $value
	 */
	public function setUserLevelAttribute($value)
	{
		$this->attributes['user_level'] = $this->getUserLevelValue($value);
	}

	/**
	 * Getter for user_type.
	 *
	 * @return string
	 */
	public function getUserTypeAttribute()
	{
		static $type;

		if ($type !== null) return $type;

		$types = array_reverse(static::$accessLevels);
		$level = $this->user_level;

		foreach ($types as $name => $min) {
			if ($level >= $min) {
				return $name;
			}
		}
	}

	/**
	 * Setter for user_type.
	 *
	 * @param string $value
	 */
	public function setUserTypeAttribute($value)
	{
		$this->setUserLevelAttribute($value);
	}

	/**
	 * Get the real user level value from a string or integer. Throws an
	 * exception if the value is invalid.
	 *
	 * @param  mixed $value
	 *
	 * @return int
	 */
	public function getUserLevelValue($value)
	{
		if (is_int($value)) {
			if ($value < 0 || $value > 255) {
				throw new \InvalidArgumentException("Invalid access level: $value");
			}
		} else {
			if (!array_key_exists($value, static::$accessLevels)) {
				throw new \InvalidArgumentException("Invalid access level: $value");
			}

			$value = static::$accessLevels[$value];
		}

		return $value;
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
		return $this->getAttribute('password');
	}

	public function getRememberToken()
	{
	    return $this->getAttribute('remember_token');
	}

	public function setRememberToken($value)
	{
		$this->setAttribute('remember_token', $value);
	}

	public function getRememberTokenName()
	{
	    return 'remember_token';
	}

	/********************
	 *    Reminders     *
	 ********************/

	public function getReminderEmail()
	{
		return $this->getAttribute('email');
	}

	/********************
	 *    Activation    *
	 ********************/

	public function activate($save = false)
	{
		$this->is_active = true;
		return $save ? $this->save() : true;
	}

	public function deactivate($save = false)
	{
		$this->is_active = false;
		return $save ? $this->save() : true;
	}

	public function getActivationEmail()
	{
		return $this->getAttribute('email');
	}
}
