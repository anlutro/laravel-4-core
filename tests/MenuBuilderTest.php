<?php
use Mockery as m;
use c\View\MenuBuilder;

class MenuBuilderTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->html = m::mock('Illuminate\Html\HtmlBuilder');
		$this->builder = new MenuBuilder($this->html);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testMakeItem()
	{
		$item = $this->builder->item('id', 'title', 'url', 'glyph');
		
		$this->assertInstanceOf('c\View\MenuItem', $item);
		$this->assertEquals('id', $item->id);
		$this->assertEquals('title', $item->title);
		$this->assertEquals('url', $item->url);
		$this->assertEquals('glyph', $item->glyph);
	}

	public function testMakeMenu()
	{
		$menu = $this->builder->make();

		$this->assertInstanceOf('c\View\MenuCollection', $menu);
		$this->assertFalse($menu->hasItems());
		$this->assertEquals(array(), $menu->getItems());
	}

	public function testAddAndGetMenu()
	{
		$menu = $this->builder->make();
		$this->builder->add('id', $menu);

		$this->assertEquals($menu, $this->builder->get('id'));
	}

	public function testAddMenuItem()
	{
		$item = $this->builder->item('id', 'title', 'url', 'glyph');
		$menu = $this->builder->make([$item]);

		$this->assertInstanceOf('c\View\MenuCollection', $menu);
		$this->assertTrue($menu->hasItems());
		$this->assertEquals(['id' => $item], $menu->getItems());
	}

	public function testRenderCallsCollectionRender()
	{
		$menu = m::mock('c\View\MenuCollection');
		$menu->shouldReceive('render')->once();
		$this->builder->add('id', $menu);

		$this->builder->render('id');
	}
}
