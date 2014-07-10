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

	public function callCreator($session)
	{
		$composer = new AlertsViewCreator($session);
		$view = m::mock('Illuminate\View\View')->makePartial();
		$composer->create($view);
		return $view;
	}

	/** @test */
	public function errorsAreAdded()
	{
		$session = $this->makeSession();
		$session->put('errors', new MessageBag(['foo', 'bar']));
		$view = $this->callCreator($session);
		$alerts = $view->alerts;
		$this->assertEquals('danger', $alerts[0]->type);
		$this->assertEquals('Foo', $alerts[0]->message);
		$this->assertEquals('danger', $alerts[1]->type);
		$this->assertEquals('Bar', $alerts[1]->message);
	}
}