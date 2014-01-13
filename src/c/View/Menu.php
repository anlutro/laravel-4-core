<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c\View;

use Illuminate\Support\Facades\Facade;

class Menu extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'menubuilder';
	}
}
