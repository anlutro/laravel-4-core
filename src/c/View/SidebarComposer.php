<?php
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
