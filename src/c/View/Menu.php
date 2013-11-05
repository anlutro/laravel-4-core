<?php
namespace c\View;

use Illuminate\Support\Facades\Facade;

class Menu extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'menubuilder';
	}
}
