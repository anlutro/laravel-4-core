<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth;

use anlutro\Core\Auth\Activation\ActivatableInterface;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Hash;

/**
 * User model.
 */
class UserModel extends \anlutro\Core\BaseModel implements UserInterface, RemindableInterface, ActivatableInterface
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
	 * Attributes that are hidden from toArray (and by extension, from toJson)
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'login_token'];

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
	 * Check if the user's password needs rehashing.
	 */
	public function rehashPassword($password)
	{
		if (!Hash::needsRehash($this->attributes['password'])) return;

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
		if (!is_int($level)) {
			if (!array_key_exists($level, static::$accessLevels)) {
				throw new \InvalidArgumentException("Invalid access level: $level");
			}

			$level = static::$accessLevels[$level];
		}

		return $query->where('user_level', '=', $level);
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

	public function activate($save = false)
	{
		$this->is_active = true;
		if ($save) return $this->save();
	}

	public function deactivate($save = false)
	{
		$this->is_active = false;
		if ($save) return $this->save();
	}

	public function getActivationEmail()
	{
		return $this->attributes['email'];
	}
}
