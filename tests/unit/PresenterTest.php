<?php
use Mockery as m;

class PresenterTest extends PHPUnit_Framework_TestCase
{
	public function tearDown()
	{
		m::close();
	}

	public function testPresenterWithStdClass()
	{
		$obj = new StdClass;
		$obj->foo = 'bar';
		$obj->bar = 'foo';
		$pres = $this->makePresenter($obj);

		$this->assertEquals('Bar', $pres->foo);
		$this->assertEquals('foo', $pres->bar);
	}

	public function testStaticMake()
	{
		$pres = PresenterStub::make(new StdClass);

		$this->assertInstanceOf('c\Presenter', $pres);
	}

	public function testStaticMakeCollection()
	{
		$collection = new Illuminate\Support\Collection(
			[new StdClass, new StdClass]
		);

		$result = PresenterStub::make($collection);

		$this->assertInstanceOf('Illuminate\Support\Collection', $result);
		$this->assertInstanceOf('c\Presenter', $result[0]);
		$this->assertInstanceOf('c\Presenter', $result[1]);
	}

	public function makePresenter($object)
	{
		return new PresenterStub($object);
	}
}

class PresenterStub extends \c\Presenter
{
	public function presentFoo($value)
	{
		return ucfirst($value);
	}
}
