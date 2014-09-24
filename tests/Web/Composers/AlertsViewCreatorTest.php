<?php
namespace anlutro\Core\Tests\Web\Composers;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Illuminate\Session\Store;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;
use anlutro\Core\Web\Composers\AlertsViewCreator;

/** @small */
class AlertsViewCreatorTest extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		m::close();
	}

	public function makeSession()
	{
		$handler = new NullSessionHandler;
		return new Store(__CLASS__, $handler);
	}

	public function mockTranslator()
	{
		$mock = m::mock('Illuminate\Translation\Translator');
		$mock->shouldReceive('get')->andReturnUsing(function($key){ return $key; })->byDefault();
		$mock->shouldReceive('has')->andReturn(true)->byDefault();
		return $mock;
	}

	public function callCreator($session, $translator = null)
	{
		if (!$translator) $translator = $this->mockTranslator();
		$composer = new AlertsViewCreator($session, $translator);
		$view = m::mock('Illuminate\View\View')->makePartial();
		$composer->create($view);
		return $view;
	}

	/** @test */
	public function errorsAreAdded()
	{
		$session = $this->makeSession();
		$session->put('errors', $bag = new MessageBag(['foo', 'bar']));
		$view = $this->callCreator($session);
		$errors = $view->validationErrors;
		$this->assertEquals($bag->all(), $errors);
	}

	/** @test */
	public function alertsAreAdded()
	{
		$session = $this->makeSession();
		$session->put('success', 'foos');
		$session->put('warning', 'foow');
		$session->put('info', 'fooi');
		$session->put('error', 'fooe');
		$view = $this->callCreator($session);
		$alerts = $view->alerts;
		$this->assertEquals('success', $alerts[0]->type);
		$this->assertEquals('Foos', $alerts[0]->message);
		$this->assertEquals('warning', $alerts[1]->type);
		$this->assertEquals('Foow', $alerts[1]->message);
		$this->assertEquals('info', $alerts[2]->type);
		$this->assertEquals('Fooi', $alerts[2]->message);
		$this->assertEquals('error', $alerts[3]->type);
		$this->assertEquals('Fooe', $alerts[3]->message);
	}
}
