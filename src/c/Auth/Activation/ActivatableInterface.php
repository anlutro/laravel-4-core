<?php
/**
 * Laravel 4 Core - Activatable interface
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth\Activation;

interface ActivatableInterface
{
	public function activate();
	public function getActivationEmail();
	public function deactivate();
}
