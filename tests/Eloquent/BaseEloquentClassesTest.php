<?php
namespace anlutro\Core\Tests\Eloquent;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use Carbon\Carbon;
use anlutro\Core\Eloquent\Model;
use anlutro\Core\Eloquent\Collection;

/** @small */
class BaseEloquentClassesTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->mockResolver = m::mock('Illuminate\Database\ConnectionResolverInterface');
		$this->mockResolver->shouldReceive('connection->getQueryGrammar->getDateFormat')
			->andReturn('Y-m-d H:i:s');
		Model::setConnectionResolver($this->mockResolver);
	}

	public function tearDown()
	{
		m::close();
	}

	/** @test */
	public function modelToStdClass()
	{
		$obj = $this->makeModel(['foo' => 'bar', 'created_at' => Carbon::now()])
			->setRelation('one', $this->makeModel(['foo' => 'bar']))
			->setRelation('many', $this->makeCollection([
				$this->makeModel(['foo' => 'bar']),
				$this->makeModel(['foo' => 'bar']),
			]))->toStdClass();

		$this->assertInstanceOf('StdClass', $obj);
		$this->assertEquals('bar', $obj->foo);
		$this->assertInstanceOf('StdClass', $obj->one);
		$this->assertInstanceOf('Carbon\Carbon', $obj->created_at);
		$this->assertEquals('bar', $obj->one->foo);
		$this->assertInternalType('array', $obj->many);
		$this->assertEquals('bar', $obj->many[0]->foo);
		$this->assertEquals('bar', $obj->many[1]->foo);
	}

	/** @test */
	public function collectionToStdClass()
	{
		$arr = $this->makeCollection([
			$this->makeModel(['foo' => 'bar']),
			$this->makeModel(['foo' => 'bar']),
			$this->makeModel(['foo' => 'bar'])
				->setRelation('one', $this->makeModel(['foo' => 'bar']))
				->setRelation('many', $this->makeCollection([
					$this->makeModel(['foo' => 'bar']),
					$this->makeModel(['foo' => 'bar']),
				])),
		])->toStdClass();

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

	/** @test */
	public function jsonEncodeModel()
	{
		$model = $this->makeModel(['id' => 1, 'foo' => 'bar', 'created_at' => $this->makeCarbon()])
			->setRelation('one', $this->makeModel(['id' => 2, 'foo' => 'bar']))
			->setRelation('many', $this->makeCollection([
				$this->makeModel(['id' => 2, 'foo' => 'bar']),
				$this->makeModel(['id' => 5, 'foo' => 'bar']),
			]));
		$str = json_encode($model);

		$this->assertEquals('{"id":1,"foo":"bar","created_at":"2014-01-01 12:00:00",'
			.'"one":{"id":2,"foo":"bar"},"many":[{"id":2,"foo":"bar"},{"id":5,"foo":"bar"}]}', $str);
	}

	/** @test */
	public function jsonEncodeCollection()
	{
		$coll = $this->makeCollection([
			$this->makeModel(['id' => 2, 'foo' => 'bar', 'created_at' => $this->makeCarbon()]),
			$this->makeModel(['id' => 5, 'foo' => 'bar']),
			$this->makeModel(['id' => 1, 'foo' => 'bar'])
				->setRelation('one', $this->makeModel(['id' => 2, 'foo' => 'bar']))
				->setRelation('many', $this->makeCollection([
					$this->makeModel(['id' => 2, 'foo' => 'bar']),
					$this->makeModel(['id' => 5, 'foo' => 'bar']),
				])),
		]);
		$str = json_encode($coll);

		$this->assertEquals('[{"id":2,"foo":"bar","created_at":"2014-01-01 12:00:00"},'
			.'{"id":5,"foo":"bar"},{"id":1,"foo":"bar","one":{"id":2,"foo":"bar"},'
			.'"many":[{"id":2,"foo":"bar"},{"id":5,"foo":"bar"}]}]', $str);
	}

	protected function makeModel($attributes)
	{
		return new BaseModelTestStub($attributes);
	}

	protected function makeCollection($items)
	{
		return new Collection($items);
	}

	protected function makeCarbon()
	{
		return Carbon::create(2014,1,1,12,0,0);
	}
}

if (!class_exists('BaseModelTestStub')) {
	class BaseModelTestStub extends Model {
		protected $guarded = [];
	}
}
