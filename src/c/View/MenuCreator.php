<?php
class MenuCreator
{
	public function create($view)
	{
		$view->with('menus', $menus);
	}
}
