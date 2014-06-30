<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Eloquent;

/**
 * Interface for classes that can be turned into a StdClass object.
 */
interface StdClassableInterface
{
	/**
	 * Turn the object into an StdClass object.
	 *
	 * @return \StdClass
	 */
	public function toStdClass();
}
