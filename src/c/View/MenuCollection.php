<?php
namespace c\View;

use Illuminate\Html\HtmlBuilder;

class MenuCollection
{
	protected $html;
	protected $items = [];

	public function __construct(HtmlBuilder $html)
	{
		$this->html = $html;
	}

	public function getItems()
	{
		return $this->items;
	}

	public function hasItems()
	{
		return !empty($this->items);
	}

	public function mergeWith(MenuCollection $menu)
	{
		foreach ($menu->getItems() as $id => $item) {
			$this->addItem($id, $item);
		}

		return true;
	}

	public function addItem($id, MenuItem $item)
	{
		if (!is_string($id)) {
			throw new \InvalidArgumentException('Menu item ID must be a string.');
		}

		if ($this->hasItem($id)) {
			$this->items[$id]->mergeWith($item);
		} else {
			$this->items[$id] = $item;
		}
	}

	public function addItems(array $items)
	{
		foreach ($items as $id => $item) {
			$this->addItem($id, $item);
		}
	}

	protected function hasItem($id)
	{
		return array_key_exists($id, $this->items);
	}

	public function render($attr = ['class' => 'navbar navbar-nav'])
	{
		$attributes = $this->html->attributes($attr);

		$items = '';
		
		foreach ($this->items as $item) {
			$items .= $item->render();
		}

		return "<ul{$attributes}>{$items}</ul>";
	}
}
