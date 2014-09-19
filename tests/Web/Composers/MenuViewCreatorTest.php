<?php
namespace anlutro\Core\Tests\Web\Composers;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use anlutro\Menu\Builder;
use anlutro\Core\Web\Composers\MenuViewCreator;

/** @small */
class MenuViewCreatorTest extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		m::close();
	}

	public function makeCreator(array $configData = array())
	{
		$this->app = new \Illuminate\Foundation\Application;
		$this->app['env'] = 'production';
		$this->builder = new Builder;
		$this->url = m::mock('Illuminate\Routing\UrlGenerator');
		$this->config = m::mock('Illuminate\Config\Repository');
		$this->config->shouldReceive('get')->andReturnUsing(function($key, $default = null) use($configData) {
			return array_get($configData, $key, $default);
		});
		return new MenuViewCreator($this->app, $this->builder, $this->url, $this->config);
	}

	public function mockView()
	{
		return m::mock('Illuminate\View\View')->makePartial();
	}

	public function callCreator()
	{
		$creator = $this->makeCreator();
		$creator->create($view = $this->mockView());
		return $view;
	}

	/** @test */
	public function menusAreAdded()
	{
		$view = $this->callCreator();
		$this->assertTrue($this->builder->hasMenu('left'));
		$this->assertTrue($this->builder->hasMenu('right'));
		$this->assertSame($this->builder->getMenu('left'), $view->menus[0]);
		$this->assertSame($this->builder->getMenu('right'), $view->menus[1]);
	}

	/** @test */
	public function homeUrlIsSetAccordingToConfig()
	{
		$creator = $this->makeCreator(['c::enable-home-link' => true, 'c::redirect-login' => '/foo']);
		$this->url->shouldReceive('to')->once()->with('/foo')->andReturn('localhost/foo');
		$creator->create($view = $this->mockView());
		$this->assertEquals('localhost/foo', $view->homeUrl);
	}

	/** @test */
	public function siteNamePrioritisesHtmlNameOverEverything()
	{
		$creator = $this->makeCreator([
			'c::site.html-name' => 'foo',
			'c::site.name' => 'bar',
			'app.url' => 'baz',
		]);
		$creator->create($view = $this->mockView());
		$this->assertEquals('foo', $view->siteName);
	}

	/** @test */
	public function siteNamePrioritisesNameOverAppUrl()
	{
		$creator = $this->makeCreator([
			'c::site.name' => 'bar',
			'app.url' => 'baz',
		]);
		$creator->create($view = $this->mockView());
		$this->assertEquals('bar', $view->siteName);
	}

	/** @test */
	public function siteNameFallsBackOnAppUrl()
	{
		$creator = $this->makeCreator(['app.url' => 'baz']);
		$creator->create($view = $this->mockView());
		$this->assertEquals('baz', $view->siteName);
	}

	/** @test */
	public function siteNameGetsEnvAppended()
	{
		$creator = $this->makeCreator(['app.url' => 'baz']);
		$this->app['env'] = 'dev';
		$creator->create($view = $this->mockView());
		$this->assertEquals('baz <strong>DEV</strong>', $view->siteName);
	}
}
