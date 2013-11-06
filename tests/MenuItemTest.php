<?php
use Mockery as m;

class MenuItemTest extends PHPUnit_Framework_TestCase
{
	public function testHasSubMenu()
	{
		$item = $this->makeItem();
		$item->subMenu = $this->makeCollection();
		$this->assertTrue($item->hasSubMenu());
	}

	public function testRenderSingle()
	{
		$item = $this->makeItem('title', 'url');
		$this->assertEquals('<a href="url">title</a>', $item->render());
	}

	public function testRenderSingleWithGlyph()
	{
		$item = $this->makeItem('title', 'url', 'glyph');
		$this->assertEquals('<a href="url"><span class="glyphicon glyphicon-glyph"></span>title</a>', $item->render());
	}

	public function testRenderWithSubMenu()
	{
		$item = $this->makeItem('title', 'url', 'glyph');
		$item->subMenu = m::mock('c\View\MenuCollection');
		$item->subMenu->shouldReceive('render')->once()->with(['class' => 'dropdown-menu'])
			->andReturn('{submenu}');

		$this->assertEquals('<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"><a href="url"><span class="glyphicon glyphicon-glyph"></span>title</a><b class="caret"></b>{submenu}</li>', $item->render());
	}

	protected function makeCollection()
	{
		return new c\View\MenuCollection(m::mock('Illuminate\Html\HtmlBuilder'));
	}

	protected function makeItem($title = '', $url = '', $glyph = null)
	{
		$item = new c\View\MenuItem;
		$item->title = $title;
		$item->url = $url;
		$item->glyph = $glyph;
		return $item;
	}
}
