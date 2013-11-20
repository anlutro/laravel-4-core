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
		$this->assertEquals('<li><a href="url">title</a></li>', $item->render());
	}

	public function testRenderSingleWithGlyph()
	{
		$item = $this->makeItem('title', 'url', 'glyph');
		$this->assertEquals('<li><a href="url"><span class="glyphicon glyphicon-glyph"></span>title</a></li>', $item->render());
	}

	public function testRenderWithSubMenu()
	{
		$item = $this->makeItem('title', 'url', 'glyph');
		$item->subMenu = m::mock('c\View\MenuCollection');
		$item->subMenu->shouldReceive('render')->once()->with(['class' => 'dropdown-menu'])
			->andReturn('{submenu}');

		$this->assertEquals('<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-glyph"></span>title<b class="caret"></b></a>{submenu}</li>', $item->render());
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
