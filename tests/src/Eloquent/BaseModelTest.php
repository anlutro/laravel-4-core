<?php
namespace anlutro\Core\Tests\Eloquent;

use Mockery as m;
use PHPUnit_Framework_TestCase;

class BaseEloquentClassesTest extends PHPUnit_Framework_TestCase
{
	public function testModelToStdClass()
	{
		$model = $this->makeModelWithRelations();
		$obj = $model->toStdClass();
		$this->assertInstanceOf('StdClass', $obj);
		$this->assertEquals('bar', $obj->foo);
		$this->assertInstanceOf('StdClass', $obj->one);
		$this->assertEquals('bar', $obj->one->foo);
		$this->assertInternalType('array', $obj->many);
		$this->assertEquals('bar', $obj->many[0]->foo);
		$this->assertEquals('bar', $obj->many[1]->foo);
	}

	public function testCollectionToStdClass()
	{
		$coll = $this->makeCollection();
		$coll->push($this->makeModelWithRelations());
		$arr = $coll->toStdClass();
		$this->assertInternalType('array', $arr);
		$this->assertInstanceOf('StdClass', $arr[0]);
		$this->assertInstanceOf('StdClass', $arr[1]);
		$this->assertEquals('bar', $arr[0]->foo);
		$this->assertEquals('bar', $arr[1]->foo);
		$this->assertInstanceOf('StdClass', $arr[2]->one);
		$this->assertEquals('bar', $arr[2]->one->foo);
		$this->assertInternalType('array', $arr[2]->many);
		$this->assertEquals('bar', $arr[2]->many[0]->foo);
		$this->assertEquals('bar', $arr[2]->many[1]->foo);
	}

	public function makeModelWithRelations()
	{
		$model = $this->makeModel();
		$model->setRelation('one', $this->makeModel());
		$model->setRelation('many', $this->makeCollection());
		return $model;
	}

	public function makeModel()
	{
		$model = new BaseModelTestStub;
		$model->foo = 'bar';
		return $model;
	}

	public function makeCollection()
	{
		$collection = new \anlutro\Core\Eloquent\Collection;
		$collection->push($this->makeModel());
		$collection->push($this->makeModel());
		return $collection;
	}
}

if (!class_exists('BaseModelTestStub')) {
	class BaseModelTestStub extends \anlutro\Core\Eloquent\Model {}
}
