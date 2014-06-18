<?php

use Mockery as m;

class TypeableModelTraitTest extends PHPUnit_Framework_TestCase
{
	public function testSetTypeAsInt()
	{
		$model = $this->makeModel();
		$model->type = 1;
		$this->assertEquals('one', $model->type);
		$this->assertSame(1, $model->type_value);
	}

	public function testSetTypeAsString()
	{
		$model = $this->makeModel();
		$model->type = 'one';
		$this->assertEquals('one', $model->type);
		$this->assertSame(1, $model->type_value);
	}

	public function testSetInvalidTypeInt()
	{
		$this->setExpectedException('InvalidArgumentException');
		$model = $this->makeModel();
		$model->type = 3;
	}

	public function testSetInvalidTypeString()
	{
		$this->setExpectedException('InvalidArgumentException');
		$model = $this->makeModel();
		$model->type = 'three';
	}

	public function testScopeWithString()
	{
		$model = $this->makeModel();
		$query = $this->makeQuery($model)->whereType('one');
		$where = $query->getQuery()->wheres[0];
		$this->assertEquals('type', $where['column']);
		$this->assertSame(1, $where['value']);
	}

	public function testScopeWithInt()
	{
		$model = $this->makeModel();
		$query = $this->makeQuery($model)->whereType(2);
		$where = $query->getQuery()->wheres[0];
		$this->assertEquals('type', $where['column']);
		$this->assertSame(2, $where['value']);
	}

	protected function makeModel()
	{
		return new TypeableModelStub;
	}

	protected function makeQuery($model)
	{
		$query = new Illuminate\Database\Eloquent\Builder(new Illuminate\Database\Query\Builder(m::mock('Illuminate\Database\ConnectionInterface'), m::mock('Illuminate\Database\Query\Grammars\Grammar'), m::mock('Illuminate\Database\Query\Processors\Processor')));
		$query->setModel($model);
		return $query;
	}
}

class TypeableModelStub extends \anlutro\Core\BaseModel
{
	protected static $types = [
		1 => 'one',
		2 => 'two',
	];

	use \anlutro\Core\Eloquent\TypeableModelTrait;
}
