<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c\View;

use Illuminate\Html\HtmlBuilder;

class MenuBuilder
{
	protected $menus = [];
	protected $html;

	/**
	 * Render a menu.
	 *
	 * @param  string $menu
	 * @param  array  $attr HTML attributes - optional
	 *
	 * @return string       Menu as HTML
	 */
	public function render($menu, $attr = ['class' => 'nav navbar-nav'])
	{
		if (!$menu = $this->get($menu)) {
			return;
		}

		return $menu->render($attr);
	}

	/**
	 * Get a menu from the builder's storage.
	 *
	 * @param  string $menu name/key of the menu.
	 *
	 * @return array|null
	 */
	public function get($menu)
	{
		if (array_key_exists($menu, $this->menus)) {
			return $this->menus[$menu];
		}
	}

	/**
	 * Add something to an existing menu or add a new menu.
	 *
	 * @param string $id
	 * @param mixed  $menu
	 */
	public function add($id, $item)
	{
		if ($item instanceof MenuCollection) {
			if (array_key_exists($id, $this->menus)) {
				$this->menus[$id]->mergeWith($item);
			} else {
				$this->menus[$id] = $item;
			}
		} elseif ($item instanceof MenuItem) {
			if (array_key_exists($id, $this->menus)) {
				$this->menus[$id]->addItem($item);
			} else {
				$this->menus[$id] = $this->make([$item]);
			}
		}
	}

	/**
	 * Create a new MenuCollection instance.
	 *
	 * @return \c\View\MenuCollection
	 */
	public function make(array $items = array())
	{
		$collection = new MenuCollection;
		
		if ($items) {
			$collection->addItems($items);
		}

		return $collection;
	}

	/**
	 * Create a new menu item with a submenu.
	 *
	 * @param  string $id
	 * @param  string $title
	 * @param  string $glyph
	 *
	 * @return \c\View\MenuItem
	 */
	public function makeDropdown($id, $title = '', $glyph = null)
	{
		$main = $this->item($id, $title, '', $glyph);
		$main->subMenu = $this->make();
		return $main;
	}

	/**
	 * Create a new menu item.
	 *
	 * @param  string $id
	 * @param  string $title
	 * @param  string $url
	 * @param  string $glyph
	 *
	 * @return \c\View\MenuItem
	 */
	public function item($id, $title = '', $url = '', $glyph = null)
	{
		$item = new MenuItem;
		$item->id = $id;
		$item->title = $title;
		$item->url = $url;
		$item->glyph = $glyph;
		return $item;
	}
}
