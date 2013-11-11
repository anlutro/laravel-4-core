<?php
/**
 * Laravel 4 Core - Sidebar composer
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
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
