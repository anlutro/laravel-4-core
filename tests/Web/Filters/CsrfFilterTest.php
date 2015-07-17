<?php

use Mockery as m;
use Illuminate\Http\Request;

class CsrfFilterTest extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		m::close();
	}

	public function makeFilter($session, $regenerate)
	{
		return new \anlutro\Core\Web\Filters\CsrfFilter($session, $regenerate);
	}

	public function mockSession()
	{
		return m::mock('Illuminate\Session\Store');
	}

	public function mockRoute()
	{
		return m::mock('Illuminate\Routing\Route');
	}

	/**
	 * @test
	 * @dataProvider getTokenData
	 */
	public function readsInputToken($throws, $input, $sessionToken)
	{
		$route = $this->mockRoute();
		$request = Request::create('/', 'POST', ['_token' => $input]);
		$session = $this->mockSession();
		$session->shouldReceive('token')->andReturn($sessionToken);
		$filter = $this->makeFilter($session, false);
		if ($throws) {
			$this->setExpectedException('Illuminate\Session\TokenMismatchException');
		}
		$filter->filter($route, $request);
	}

	/**
	 * @test
	 * @dataProvider getTokenData
	 */
	public function readsCookieToken($throws, $input, $sessionToken)
	{
		$route = $this->mockRoute();
		$request = Request::create('/', 'POST', []);
		$request->cookies->set('X-XSRF-TOKEN', $input);
		$session = $this->mockSession();
		$session->shouldReceive('token')->andReturn($sessionToken);
		$filter = $this->makeFilter($session, false);
		if ($throws) {
			$this->setExpectedException('Illuminate\Session\TokenMismatchException');
		}
		$filter->filter($route, $request);
	}

	public function getTokenData()
	{
		return [
			[true, 'foo', 'bar'],
			[true, '', 'bar'],
			[true, '', '0asdf'],
			[true, 0, '0asdf'],
			[true, 0, 'foo'],
			[true, 1, '1asdf'],
			[false, '0asdf', '0asdf'],
		];
	}

	/** @test */
	public function regeneratesTokenWhenConfigured()
	{
		$route = $this->mockRoute();
		$request = Request::create('/', 'POST', ['_token' => 'asdf']);
		$session = $this->mockSession();
		$session->shouldReceive('token')->andReturn('asdf');
		$session->shouldReceive('regenerateToken')->once();
		$filter = $this->makeFilter($session, true);
		$filter->filter($route, $request);
	}
}
