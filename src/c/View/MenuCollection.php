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

	/**
	 * Get the items of the collection.
	 *
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * Check if a menu has any items.
	 *
	 * @return boolean
	 */
	public function hasItems()
	{
		return !empty($this->items);
	}

	/**
	 * Merge another collection into this one.
	 *
	 * @param  \c\View\MenuCollection $menu
	 *
	 * @return void
	 */
	public function mergeWith(MenuCollection $menu)
	{
		foreach ($menu->getItems() as $item) {
			$this->addItem($item);
		}
	}

	/**
	 * Add an item to the collection. Merges if one with the same unique ID
	 * already exists.
	 *
	 * @param \c\View\MenuItem $item
	 */
	public function addItem(MenuItem $item)
	{
		$id = $item->id;

		if (!is_string($id)) {
			throw new \InvalidArgumentException('Menu item ID must be a string.');
		}

		if ($this->hasItem($id)) {
			$this->items[$id]->mergeWith($item);
		} else {
			$this->items[$id] = $item;
		}
	}

	/**
	 * Add an array of items to the collection.
	 *
	 * @param array $items
	 */
	public function addItems(array $items)
	{
		foreach ($items as $item) {
			$this->addItem($item);
		}
	}

	/**
	 * Check if a menu item with a certain unique ID is in the collection.
	 *
	 * @param  string  $id
	 *
	 * @return boolean
	 */
	protected function hasItem($id)
	{
		return array_key_exists($id, $this->items);
	}

	/**
	 * Render the collection.
	 *
	 * @param  array  $attr HTML attributes
	 *
	 * @return string
	 */
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
