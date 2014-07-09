<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Web\Composers;

use anlutro\Menu\Builder;
use Illuminate\View\View;

class SidebarLayoutCreator
{
	protected $menu;

	public function __construct(Builder $menu)
	{
		$this->menu = $menu;
	}

	public function create(View $view)
	{
		$view->sidebar = $this->getSidebarMenu();
	}

	protected function getSidebarMenu()
	{
		return $this->menu->hasMenu('sidebar')
			? $this->menu->getMenu('sidebar')
			: $this->menu->createMenu('sidebar', ['class' => '']);
	}
}
