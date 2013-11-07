<?php
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
