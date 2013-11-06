<?php
use Mockery as m;
use c\View\MenuCollection;
use c\View\MenuItem;

class MenuCollectionTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->html = m::mock('Illuminate\Html\HtmlBuilder');
		$this->coll = $this->makeCollection();
	}

	public function tearDown()
	{
		m::close();
	}

	public function testAddItems()
	{
		$item1 = $this->makeItem('one', 'title', 'url', 'glyph');
		$item2 = $this->makeItem('two', 'title', 'url', 'glyph');
		$this->coll->addItem($item1);
		$this->coll->addItem($item2);

		$this->assertTrue($this->coll->hasItems());
		$this->assertEquals(['one' => $item1, 'two' => $item2], $this->coll->getItems());
	}

	public function testAddExistingItem()
	{
		$item1 = $this->makeItem('one', 'title', 'url', 'glyph');
		$item2 = $this->makeItem('one', 'title2', 'url2', 'glyph2');
		$this->coll->addItem($item1);
		$this->coll->addItem($item2);

		$items = $this->coll->getItems();
		$this->assertEquals('title2', $items['one']->title);
		$this->assertEquals('url2', $items['one']->url);
		$this->assertEquals('glyph2', $items['one']->glyph);
	}

	public function testAddItemWithSubmenu()
	{
		$item = $this->makeItem('id', 'title', 'url', 'glyph');
		$item->subMenu = $this->makeCollection();
		$this->assertTrue($item->hasSubMenu());

		$this->coll->addItem($item);
		$items = $this->coll->getItems();
		$this->assertTrue($items['id']->hasSubmenu());
	}

	public function testMerge()
	{
		$item1 = $this->makeItem('one', 'title1', 'url1', 'glyph1');
		$this->coll->addItem($item1);

		$newColl = $this->makeCollection();
		$item2 = $this->makeItem('two', 'title2', 'url2', 'glyph2');
		$item3 = $this->makeItem('three', 'title3', 'url3', 'glyph3');
		$newColl->addItem($item2);
		$newColl->addItem($item3);

		$this->coll->mergeWith($newColl);
		$items = $this->coll->getItems();
		$this->assertEquals(3, count($items));
		$this->assertTrue(array_key_exists('one', $items));
		$this->assertTrue(array_key_exists('two', $items));
		$this->assertTrue(array_key_exists('three', $items));
	}

	public function testMergeSubMenu()
	{
		$item = $this->makeItem('id', 'title', 'url', 'glyph');
		$this->coll->addItem($item);

		$newColl = $this->makeCollection();
		$item = $this->makeItem('id', 'title', 'url', 'glyph');
		$subMenu = $this->makeCollection();
		$subMenu->addItem($this->makeItem('id', 'title', 'url', 'glyph'));
		$item->subMenu = $subMenu;
		$newColl->addItem($item);

		$this->coll->mergeWith($newColl);
		$items = $this->coll->getItems();
		$this->assertTrue($items['id']->hasSubmenu());
	}

	protected function makeCollection()
	{
		return new MenuCollection($this->html);
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
