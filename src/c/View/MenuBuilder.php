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

		return $this->renderItems($menu, $attr);
	}

	/**
	 * Render an array of menu items.
	 *
	 * @param  array $menu
	 * @param  array $attr HTML attributes - optional
	 *
	 * @return string      Menu as HTML
	 */
	public function renderItems($menu, $attr = ['class' => 'nav navbar-nav'])
	{
		$items = $this->build($menu);

		return $this->html->ul($items, $attr);
	}

	/**
	 * Build a menu into an array ready to be passed to HTML::ul
	 *
	 * @param  array   $menu
	 * @param  boolean $submenu Whether this is a submenu or not. Default false
	 *
	 * @return array
	 */
	public function build($menu, $submenu = false)
	{
		$items = [];

		foreach ($menu as $key => $item) {
			if (!empty($item['submenu'])) {
				$html = '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown">'
					. $this->renderItem($item) . '<b class="caret"></b>'
					. $this->renderItems($item['submenu'], ['class' => 'dropdown-menu'])
					. '</li>';
			} else {
				$html = '<li>' . $this->renderItem($item) . '</li>';
			}
			$items[] = $html;
		}
		
		return $items;
	}

	/**
	 * Render a single menu item.
	 *
	 * @param  array $item
	 *
	 * @return string
	 */
	protected function renderItem($item)
	{
		$html = '';

		if (!empty($item['url'])) {
			$html .= '<a href="'.$item['url'].'">';
		}

		if (!empty($item['glyph'])) {
			$html .= $this->glyphicon($item['glyph']);
		}

		$html .= $item['title'];

		if (!empty($item['url'])) {
			$html .= '</a>';
		}

		return $html;
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
	 * Render the glyphicon HTML.
	 *
	 * @param  string $glyph
	 *
	 * @return string
	 */
	protected function glyphicon($glyph)
	{
		return '<span class="glyphicon glyphicon-'.$glyph.'"></span>';
	}
}
