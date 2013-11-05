<?php
abstract class MenuComposer
{
	final public function compose($view)
	{
		$this->view = $view;
		$this->addMenuItems();
	}

	protected function addMenuItems(){}

	protected function add($location, array $menu)
	{
		
	}
}
