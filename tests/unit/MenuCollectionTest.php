<?php
use Mockery as m;
use c\View\MenuCollection;
use c\View\MenuItem;

class MenuCollectionTest extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		m::close();
	}

	public function testAddItems()
	{
		$coll = new MenuCollection;

		$item1 = $this->makeItem('one', 'title', 'url', 'glyph');
		$item2 = $this->makeItem('two', 'title', 'url', 'glyph');
		$coll->addItem($item1);
		$coll->addItem($item2);

		$this->assertTrue($coll->hasItems());
		$this->assertEquals(['one' => $item1, 'two' => $item2], $coll->getItems());
	}

	public function testAddExistingItem()
	{
		$coll = new MenuCollection;

		$item1 = $this->makeItem('one', 'title', 'url', 'glyph');
		$item2 = $this->makeItem('one', 'title2', 'url2', 'glyph2');
		$coll->addItem($item1);
		$coll->addItem($item2);

		$items = $coll->getItems();
		$this->assertEquals('title2', $items['one']->title);
		$this->assertEquals('url2', $items['one']->url);
		$this->assertEquals('glyph2', $items['one']->glyph);
	}

	public function testAddItemWithSubmenu()
	{
		$coll = new MenuCollection;

		$item = $this->makeItem('id', 'title', 'url', 'glyph');
		$item->subMenu = new MenuCollection;
		$this->assertTrue($item->hasSubMenu());

		$coll->addItem($item);
		$items = $coll->getItems();
		$this->assertTrue($items['id']->hasSubmenu());
	}

	public function testMerge()
	{
		$coll = new MenuCollection;

		$item1 = $this->makeItem('one', 'title1', 'url1', 'glyph1');
		$coll->addItem($item1);

		$newColl = new MenuCollection;
		$item2 = $this->makeItem('two', 'title2', 'url2', 'glyph2');
		$item3 = $this->makeItem('three', 'title3', 'url3', 'glyph3');
		$newColl->addItem($item2);
		$newColl->addItem($item3);

		$coll->mergeWith($newColl);
		$items = $coll->getItems();
		$this->assertEquals(3, count($items));
		$this->assertTrue(array_key_exists('one', $items));
		$this->assertTrue(array_key_exists('two', $items));
		$this->assertTrue(array_key_exists('three', $items));
	}

	public function testMergeSubMenu()
	{
		$coll = new MenuCollection;

		$item = $this->makeItem('id', 'title', 'url', 'glyph');
		$coll->addItem($item);

		$newColl = new MenuCollection;
		$item = $this->makeItem('id', 'title', 'url', 'glyph');
		$subMenu = new MenuCollection;
		$subMenu->addItem($this->makeItem('id', 'title', 'url', 'glyph'));
		$item->subMenu = $subMenu;
		$newColl->addItem($item);

		$coll->mergeWith($newColl);
		$items = $coll->getItems();
		$this->assertTrue($items['id']->hasSubmenu());
	}

	protected function makeItem($id, $title, $url, $glyph = null)
	{
		$item = new MenuItem;
		$item->id = $id;
		$item->title = $title;
		$item->url = $url;
		$item->glyph = $glyph;
		return $item;
	}
}
