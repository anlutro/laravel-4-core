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
use Illuminate\Support\Facades\Hash;

class UserModel extends \c\BaseModel implements RemindableInterface, ActivatableInterface
{
	/**
	 * The database table the model queries from.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * Whether or not the model soft deletes.
	 *
	 * @var boolean
	 */
	protected $softDelete = true;

	/**
	 * The attributes that are fillable by mass assignment.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'phone', 'password'];

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
	public function scopeFilterUserLevel($query, $level)
	{
		return $query->where('user_level', '>=', $level);
	}

	/**
	 * Setter for is_active.
	 */
	public function setIsActiveAttribute($value)
	{
		$this->attributes['is_active'] = $value ? '1' : '0';
	}

	/**
	 * Make sure is_active returns the proper boolean.
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

		if (!array_key_exists($access, static::$accessLevels)) {
			throw new \InvalidArgumentException("Invalid access level: $access");
		}

		return $this->user_level >= static::$accessLevels[$access];
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
		if (!is_int($value)) {
			if (!array_key_exists($value, static::$accessLevels)) {
				throw new \InvalidArgumentException("Invalid access level: $access");
			}

			$value = static::$accessLevels[$value];
		}

		$this->attributes['user_level'] = $value;
	}

	/**
	 * Getter for user_type.
	 *
	 * @return string
	 */
	public function getUserTypeAttribute()
	{
		$types = static::$accessLevels;
		$level = $this->user_level;

		foreach ($types as $name => $min) {
			if ($level <= $min) {
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
		$this->is_active = true;
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
