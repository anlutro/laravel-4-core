<?php
namespace c\View;

use Illuminate\Html\HtmlBuilder;

class MenuBuilder
{
	protected $menus = [];
	protected $html;

	public function __construct(HtmlBuilder $html)
	{
		$this->html = $html;
	}

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
	public function add($id, MenuCollection $menu)
	{
		if (array_key_exists($id, $this->menus)) {
			$this->menus[$id]->mergeWith($menu);
		} else {
			$this->menus[$id] = $menu;
		}
	}

	/**
	 * Create a new MenuCollection instance.
	 *
	 * @return \c\View\MenuCollection
	 */
	public function make(array $items = array())
	{
		$collection = new MenuCollection($this->html);
		
		if ($items) {
			$collection->addItems($items);
		}

		return $collection;
	}

	public function item($title, $url, $glyph = null)
	{
		$item = new MenuItem;
		$item->title = $title;
		$item->url = $url;
		$item->glyph = $glyph;
		return $item;
	}
}
