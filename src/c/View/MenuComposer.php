<?php
/**
 * Laravel 4 Core - Menu composer
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\View;

abstract class MenuComposer
{
	final public function compose($view)
	{
		$this->view = $view;
		$this->addItems();
	}

	protected function add($id, $item)
	{
		Menu::add($id, $item);
	}

	abstract protected function addItems();
}
