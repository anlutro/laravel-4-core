<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Activation;

/**
 * Interface a user model that can be activated needs to implement.
 */
interface ActivatableInterface
{
	/**
	 * Activate a user.
	 *
	 * @return void
	 */
	public function activate();

	/**
	 * Deactivate a user.
	 *
	 * @return void
	 */
	public function deactivate();

	/**
	 * Get the email that should be used to send activation links.
	 *
	 * @return string
	 */
	public function getActivationEmail();
}
