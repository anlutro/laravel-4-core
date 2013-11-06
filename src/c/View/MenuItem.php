<?php
namespace c\View;

class MenuItem
{
	public $id;
	public $title;
	public $glyph;
	public $url;
	public $subMenu;

	public function __construct()
	{
		// ...
	}

	/**
	 * Check if the menu item has a submenu.
	 *
	 * @return boolean
	 */
	public function hasSubMenu()
	{
		return !empty($this->subMenu);
	}

	/**
	 * Merge another menu item into this one.
	 *
	 * @param  \c\View\MenuItem $item
	 *
	 * @return void
	 */
	public function mergeWith(MenuItem $item)
	{
		if ($item->hasSubMenu() && $this->hasSubMenu()) {
			// both items have a submenu, so we merge the submenus
			$this->subMenu->mergeWith($item->subMenu);
		} elseif ($item->hasSubMenu() && !$this->hasSubMenu()) {
			// simply add the items submenu as our own
			$this->subMenu = $item->subMenu;
		} else {
			$this->title = $item->title;
			$this->glyph = $item->glyph;
			$this->url = $item->url;
		}
	}

	/**
	 * Render the menu item.
	 *
	 * @return string
	 */
	public function render()
	{
		$html = '';

		if ($this->hasSubMenu()) {
			$html .= '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown">';
		} else {
			$html .= '<li>';

			if (!empty($this->url)) {
				$html .= '<a href="'.$this->url.'">';
			}
		}

		if (!empty($this->glyph)) {
			$html .= '<span class="glyphicon glyphicon-'.$this->glyph.'"></span>';
		}

		$html .= $this->title;

		if ($this->hasSubMenu()) {
			$html .= '<b class="caret"></b></a>' . $this->subMenu->render(['class' => 'dropdown-menu']);
		} elseif (!empty($this->url)) {
			$html .= '</a>';
		}

		$html .= '</li>';

		return $html;
	}
}
