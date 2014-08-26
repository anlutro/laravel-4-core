<?php
namespace anlutro\Core\Tests\Web\Composers;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use anlutro\Core\Html\ScriptManager;
use anlutro\Core\Web\Composers\GenericLayoutCreator;

/** @small */
class GenericLayoutCreatorTest extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		m::close();
	}

	public function makeCreator(array $configData, array $translations)
	{
		$this->app = new \Illuminate\Foundation\Application;
		$this->app['env'] = 'production';
		$this->config = m::mock('Illuminate\Config\Repository');
		$this->config->shouldReceive('get')->andReturnUsing(function($key, $default = null) use($configData) {
			return array_get($configData, $key, $default);
		});
		$this->translator = m::mock('Illuminate\Translation\Translator');
		$this->translator->shouldReceive('getLocale')->andReturn('en');
		$this->translator->shouldReceive('get')->andReturnUsing(function($key) use($translations) {
			return array_get($translations, $key);
		});
		$this->scripts = new ScriptManager;
		return new GenericLayoutCreator($this->app, $this->config, $this->translator, $this->scripts);
	}

	public function callCreator(array $config, array $translations)
	{
		$creator = $this->makeCreator($config, $translations);
		$view = m::mock('Illuminate\View\View')->makePartial();
		$creator->create($view);
		return $view;
	}

	/** @test */
	public function configAndTranslatorValuesAreAdded()
	{
		$config = ['c::site.name' => 'title', 'c::site.ga-code' => 'code'];
		$translations = ['c::site.description' => 'description'];
		$view = $this->callCreator($config, $translations);
		$this->assertEquals('title', $view->title);
		$this->assertEquals('description', $view->description);
		$this->assertEquals('code', $view->gaCode);
	}

	/** @test */
	public function envIsAppendedToTitle()
	{
		$config = ['c::site.name' => 'title'];
		$creator = $this->makeCreator($config, []);
		$this->app['env'] = 'dev';
		$creator->create($view = m::mock('Illuminate\View\View')->makePartial());
		$this->assertEquals('title (DEV)', $view->title);
	}
}
