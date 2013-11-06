<?php
namespace c\View;

class MenuItem
{
	public $title;
	public $glyph;
	public $url;
	public $subMenu;

	public function __construct()
	{
		// ...
	}

	public function hasSubMenu()
	{
		return !empty($this->subMenu);
	}

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

	public function render()
	{
		if ($this->hasSubMenu()) {
			return $this->renderWithSub();
		} else {
			return $this->renderSingle();
		}

		
	}

	protected function renderSingle()
	{
		$html = '';

		if (!empty($this->url)) {
			$html .= '<a href="'.$this->url.'">';
		}

		if (!empty($this->glyph)) {
			$html .= '<span class="glyphicon glyphicon-'.$this->glyph.'"></span>';
		}

		$html .= $this->title;

		if (!empty($this->url)) {
			$html .= '</a>';
		}

		return $html;
	}

	protected function renderWithSub()
	{
		return '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown">'
			. $this->renderSingle() . '<b class="caret"></b>'
			. $this->subMenu->render(['class' => 'dropdown-menu'])
			. '</li>';
	}
}
