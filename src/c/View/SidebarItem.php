<?php
/**
 * Laravel 4 Core - Sidebar item
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\View;

class SidebarItem
{
	public $title;
	public $content;

	public function __construct($title = null, $content = null)
	{
		$this->title = $title;
		$this->content = $content;
	}
}
