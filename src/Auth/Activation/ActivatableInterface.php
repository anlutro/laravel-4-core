<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c\Auth\Activation;

/**
 * Interface a user model that can be activated needs to implement.
 */
interface ActivatableInterface
{
	public function activate();
	public function getActivationEmail();
	public function deactivate();
}
