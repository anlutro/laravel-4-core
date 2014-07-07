<?php
namespace anlutro\Core\Tests\Eloquent;

use anlutro\LaravelTesting\EloquentTestCase;
use Mockery as m;

/** @small */
class ImmutableModelTest extends EloquentTestCase
{
	/** @test */
	public function cannotSaveImmutableModel()
	{
		$model = new TestModel;
		$this->setExpectedException('RuntimeException');
		$model->save();
	}

	/** @test */
	public function cannotSetAttributeOnImmutableModel()
	{
		$model = new TestModel;
		$model->makeMutable();
		$model->foo = 'bar';
		$model->makeImmutable();
		$this->setExpectedException('RuntimeException');
		$model->foo = 'bar';
	}
}

class TestModel extends \Illuminate\Database\Eloquent\Model
{
	use \anlutro\Core\Eloquent\ImmutableTrait;
}
