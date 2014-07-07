<?php
namespace anlutro\Core\Tests\Web\Composers;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use anlutro\Core\Web\Composers\MainLayoutCreator;

/** @small */
class MainLayoutCreatorTest extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		m::close();
	}

	public function makeCreator(array $configData, array $translations)
	{
		$config = m::mock('Illuminate\Config\Repository');
		$config->shouldReceive('get')->andReturnUsing(function($key, $default = null) use($configData) {
			return array_get($configData, $key, $default);
		});
		$translator = m::mock('Illuminate\Translation\Translator');
		$translator->shouldReceive('get')->andReturnUsing(function($key) use($translations) {
			return array_get($translations, $key);
		});
		return new MainLayoutCreator($config, $translator);
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
}
