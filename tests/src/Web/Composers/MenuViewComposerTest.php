<?php
namespace anlutro\Core\Tests\Web\Composers;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use anlutro\Menu\Builder;
use anlutro\Core\Web\Composers\MenuViewComposer;

class MenuViewComposerTest extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		m::close();
	}

	public function makeComposer(array $configData = array())
	{
		$this->builder = new Builder;
		$this->auth = m::mock('Illuminate\Auth\AuthManager');
		$this->auth->shouldReceive('check')->once()->andReturn(false)->byDefault();
		$this->url = m::mock('Illuminate\Routing\UrlGenerator');
		$this->config = m::mock('Illuminate\Config\Repository');
		$this->config->shouldReceive('get')->andReturnUsing(function($key, $default = null) use($configData) {
			return array_get($configData, $key, $default);
		});
		return new MenuViewComposer($this->builder, $this->auth, $this->url, $this->config);
	}

	public function mockView()
	{
		return m::mock('Illuminate\View\View')->makePartial();
	}

	public function callComposer()
	{
		$composer = $this->makeComposer();
		$composer->compose($view = $this->mockView());
		return $view;
	}

	/** @test */
	public function noMenusAddsEmptyArray()
	{
		$view = $this->callComposer();
		$this->assertEquals([], $view->menus);
	}

	/** @test */
	public function emptyMenuIsNotAdded()
	{
		$composer = $this->makeComposer();
		$this->builder->createMenu('left');
		$composer->compose($view = $this->mockView());
		$this->assertEquals(0, count($view->menus));
	}

	/** @test */
	public function nonEmptyMenuIsAdded()
	{
		$composer = $this->makeComposer();
		$this->builder->createMenu('left')->addItem('foo', 'bar');
		$composer->compose($view = $this->mockView());
		$this->assertEquals(1, count($view->menus));
	}

	/** @test */
	public function homeUrlIsNotSetForGuests()
	{
		$composer = $this->makeComposer();
		$this->auth->shouldReceive('check')->once()->andReturn(false);
		$composer->compose($view = $this->mockView());
		$this->assertEquals(null, $view->homeUrl);
	}

	/** @test */
	public function homeUrlIsSetForLoggedInUsers()
	{
		$composer = $this->makeComposer(['c::redirect-login' => '/foo']);
		$this->auth->shouldReceive('check')->once()->andReturn(true);
		$this->url->shouldReceive('to')->once()->with('/foo')->andReturn('localhost/foo');
		$composer->compose($view = $this->mockView());
		$this->assertEquals('localhost/foo', $view->homeUrl);
	}

	/** @test */
	public function siteNamePrioritisesHtmlNameOverEverything()
	{
		$composer = $this->makeComposer([
			'c::site.html-name' => 'foo',
			'c::site.name' => 'bar',
			'app.url' => 'baz',
		]);
		$composer->compose($view = $this->mockView());
		$this->assertEquals('foo', $view->siteName);
	}

	/** @test */
	public function siteNamePrioritisesNameOverAppUrl()
	{
		$composer = $this->makeComposer([
			'c::site.name' => 'bar',
			'app.url' => 'baz',
		]);
		$composer->compose($view = $this->mockView());
		$this->assertEquals('bar', $view->siteName);
	}

	/** @test */
	public function siteNameFallsBackOnAppUrl()
	{
		$composer = $this->makeComposer(['app.url' => 'baz']);
		$composer->compose($view = $this->mockView());
		$this->assertEquals('baz', $view->siteName);
	}
}
