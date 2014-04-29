<?php
namespace anlutro\Core\Tests;

use anlutro\LaravelTesting\EloquentTestCase;

class SearchableTraitTest extends EloquentTestCase
{
	protected function makeModel()
	{
		return new StubModel;
	}

	/** @test */
	public function basicSearch()
	{
		$model = $this->makeModel();
		$query = $model->newQuery()->where('foo', '=', 'bar')->search('foo');
		$this->assertEquals('select * from "stub_models" where "foo" = ? and ("field1" like ? or "field2" like ?)', $query->toSql());
		$this->assertEquals(['bar', '%foo%', '%foo%'], $query->getBindings());
	}
}

class StubModel extends \Illuminate\Database\Eloquent\Model
{
	use \anlutro\Core\ModelTraits\Searchable;
	protected $searchable = ['field1', 'field2'];
}
