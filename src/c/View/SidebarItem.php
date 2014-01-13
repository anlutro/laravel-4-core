<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
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
