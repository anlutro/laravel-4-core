<?php
/**
 * Laravel 4 Core - Activation facade
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth\Activation;

use Illuminate\Support\Facades\Facade;

/**
 * @see  c\Auth\Activation\ActivationBroker
 */
class Activation extends Facade
{
	public static function getFacadeAccessor()
	{
		return 'auth.activation';
	}
}
