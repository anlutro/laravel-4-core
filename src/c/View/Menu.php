<?php
/**
 * Laravel 4 Core - Menu builder facade
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
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
