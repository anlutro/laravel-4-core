<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Activation;

use Illuminate\Support\Facades\Facade;

/**
 * @see  anlutro\Core\Auth\Activation\ActivationBroker
 */
class Activation extends Facade
{
	public static function getFacadeAccessor()
	{
		return 'auth.activation';
	}
}
