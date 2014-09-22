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
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Illuminate\Support\Facades\Hash;

use anlutro\Core\Auth\Activation\ActivatableInterface;
use anlutro\Core\Auth\Reminders\RemindableInterface;
use anlutro\Core\Eloquent\Model;

/**
 * User model.
 */
class UserModel extends Model implements UserInterface, RemindableInterface, ActivatableInterface
{
	use SoftDeletingTrait;
	use Traits\PasswordModelTrait;
	use Traits\ActivatableModelTrait;
	use Traits\UserLevelModelTrait;

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
	 * Attributes that should be represented as Carbon objects.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at', 'last_login'];

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

	public function setPassword($newPassword)
	{
		$this->setPasswordAttribute($newPassword);

		$this->save();
	}
}
