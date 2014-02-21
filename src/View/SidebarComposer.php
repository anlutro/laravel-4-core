<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c\View;

abstract class SidebarComposer
{
	final public function compose($view)
	{
		$this->view = $view;
		$this->addItems();
	}

	public function add(SidebarItem $item)
	{
		$this->view->sidebar[] = $item;
	}
}
