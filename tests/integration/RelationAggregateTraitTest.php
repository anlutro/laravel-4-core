<?php

class RelationAggregateTraitTest extends \anlutro\LaravelTesting\EloquentTestCase
{
	public function setUp()
	{
		parent::setUp();

		// unguard all models to allow mass assignment
		Illuminate\Database\Eloquent\Model::unguard();
	}

	protected function getMigrations() {}

	/**
	 * Overwrite the runMigrations method as we won't be using the default
	 * class-based migration system for this test.
	 */
	protected function runMigrations($direction)
	{
		$schema = $this->capsule->schema();

		if ($direction == 'down') {
			$schema->drop('models');
			$schema->drop('relations');
			$schema->drop('pivot');
			return;
		}

		$schema->create('models', function($t) {
			$t->increments('id');
			$t->string('name', 32);
		});
		$schema->create('relations', function($t) {
			$t->increments('id');
			$t->integer('model_id')->unsigned()->nullable();
			$t->string('name', 32);
		});
		$schema->create('pivot', function($t) {
			$t->integer('model_id')->unsigned();
			$t->integer('relation_id')->unsigned();
		});
	}

	public function testModelsWork()
	{
		$m = RelAggrModelStub::create(['name' => 'foo']);
		$r = RelatedStub::create(['name' => 'bar']);
		$m->mtmrelation()->attach($r->id);

		$this->assertEquals('foo', $m->name);
		$this->assertEquals('bar', $r->name);
		$this->assertEquals('bar', $m->mtmrelation->first()->name);
	}

	public function testLoadRelationCount()
	{
		$m = RelAggrModelStub::create(['name' => 'foo']);
		$r = new RelatedStub(['name' => 'bar']);
		$r->btrelation()->associate($m);
		$r->save();
		$m->loadRelationCount('hmrelation');
		$r->loadRelationCount('btrelation');

		$this->assertEquals('foo', $r->btrelation->name);
		$this->assertEquals('bar', $m->horelation->name);
		$this->assertEquals(1, $m->hmrelation_count);
		$this->assertEquals(1, $r->btrelation_count);
	}

	public function testLoadAggregate()
	{
		$m = RelAggrModelStub::create(['name' => 'foo']);
		$r = new RelatedStub(['name' => 'bar']);
		$r->btrelation()->associate($m);
		$r->save();
		$r = new RelatedStub(['name' => 'bar']);
		$r->btrelation()->associate($m);
		$r->save();
		$m->loadRelationAggregate('hmrelation', 'sum', 'id');
		$r->loadRelationAggregate('btrelation', 'sum', 'id');

		$this->assertEquals(3, $m->hmrelation_sum_id);
		$this->assertEquals(1, $r->btrelation_sum_id);
	}

	public function testEagerLoadRelationCount()
	{
		$m = RelAggrModelStub::create(['name' => 'foo']);
		$r = RelatedStub::create(['name' => 'bar']);
		$m->mtmrelation()->attach($r->id);
		$r = RelatedStub::create(['name' => 'baz']);
		$m->mtmrelation()->attach($r->id);

		$models = RelAggrModelStub::withRelationCount('mtmrelation');
		$this->assertEquals(2, $models->first()->mtmrelation_count);
	}

	public function testEagerLoadRelationAggregate()
	{
		$m = RelAggrModelStub::create(['name' => 'foo']);
		$r = RelatedStub::create(['name' => 'bar']);
		$m->mtmrelation()->attach($r->id);
		$r = RelatedStub::create(['name' => 'baz']);
		$m->mtmrelation()->attach($r->id);

		$models = RelAggrModelStub::withRelationAggregate(['mtmrelation' => ['sum', 'id']]);
		$this->assertEquals(3, $models->first()->mtmrelation_sum_id);
	}

	public function testEagerLoadRelationAggregateWithConstraint()
	{
		$m = RelAggrModelStub::create(['name' => 'foo']);
		$r = RelatedStub::create(['name' => 'bar']);
		$m->mtmrelation()->attach($r->id);
		$r = RelatedStub::create(['name' => 'baz']);
		$m->mtmrelation()->attach($r->id);

		$constraint = function($query) {
			$query->where('id', '=', 1);
		};

		$models = RelAggrModelStub::withRelationAggregate(['mtmrelation' => ['sum', 'id', $constraint]]);
		$this->assertEquals(1, $models->first()->mtmrelation_sum_id);
	}
}

class RelAggrModelStub extends \Illuminate\Database\Eloquent\Model
{
	protected $table = 'models';
	public $timestamps = false;

	use anlutro\Core\ModelTraits\AddSubSelect;
	use anlutro\Core\ModelTraits\RelationAggregates;

	public function mtmrelation()
	{
		return $this->belongsToMany('RelatedStub', 'pivot', 'model_id', 'relation_id');
	}

	public function hmrelation()
	{
		return $this->hasMany('RelatedStub', 'model_id');
	}

	public function horelation()
	{
		return $this->hasOne('RelatedStub', 'model_id');
	}
}

class RelatedStub extends \Illuminate\Database\Eloquent\Model
{
	protected $table = 'relations';
	public $timestamps = false;

	use anlutro\Core\ModelTraits\AddSubSelect;
	use anlutro\Core\ModelTraits\RelationAggregates;

	public function btrelation()
	{
		return $this->belongsTo('RelAggrModelStub', 'model_id');
	}
}
