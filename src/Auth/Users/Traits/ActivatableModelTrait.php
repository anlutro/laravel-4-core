<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Users\Traits;

trait ActivatableModelTrait
{
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

	public function activate()
	{
		$this->is_active = true;

		$this->save();
	}

	public function deactivate()
	{
		$this->is_active = false;

		$this->save();
	}

	public function getActivationEmail()
	{
		return $this->getAttribute('email');
	}
}
