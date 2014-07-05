<?php
namespace anlutro\Core\Tests\Eloquent;

use anlutro\LaravelTesting\EloquentTestCase;
use anlutro\Core\Eloquent\RelationshipQueryJoiner;

class RelationshipQueryJoinerTest extends EloquentTestCase
{
	/** @test */
	public function joinBelongsTo()
	{
		$sql = 'select "stub_models".* from "stub_models"
			left join "stub_model_ones" on "stub_models"."foreign_key" = "stub_model_ones"."id"';
		$query = (new StubModel)->newQuery();
		(new RelationshipQueryJoiner($query))->join('one');
		$this->assertEquals($this->trimSql($sql), $query->toSql());
	}

	/** @test */
	public function joinHasMany()
	{
		$sql = 'select "stub_models".* from "stub_models"
			left join "stub_model_twos" on "stub_model_twos"."foreign_key" = "stub_models"."id"';
		$query = (new StubModel)->newQuery();
		(new RelationshipQueryJoiner($query))->join('two');
		$this->assertEquals($this->trimSql($sql), $query->toSql());
	}

	/** @test */
	public function joinBelongsToMany()
	{
		$sql = 'select "stub_models".* from "stub_models"
			left join "pivot_table" on "pivot_table"."local_key" = "stub_models"."id"
			left join "stub_model_threes" on "pivot_table"."foreign_key" = "stub_model_threes"."id"';
		$query = (new StubModel)->newQuery();
		(new RelationshipQueryJoiner($query))->join('three');
		$this->assertEquals($this->trimSql($sql), $query->toSql());
	}

	/** @test */
	public function joinNestedBelongsToAndHasMany()
	{
		$sql = 'select "stub_models".* from "stub_models"
			left join "stub_model_ones" on "stub_models"."foreign_key" = "stub_model_ones"."id"
			left join "stub_model_threes" on "stub_model_threes"."foreign_key" = "stub_model_ones"."id"';
		$query = (new StubModel)->newQuery();
		(new RelationshipQueryJoiner($query))->join('one.five');
		$this->assertEquals($this->trimSql($sql), $query->toSql());
	}

	/** @test */
	public function joinSeveral()
	{
		$sql = 'select "stub_models".* from "stub_models"
			left join "stub_model_ones" on "stub_models"."foreign_key" = "stub_model_ones"."id"
			left join "stub_model_twos" on "stub_model_twos"."foreign_key" = "stub_models"."id"';
		$query = (new StubModel)->newQuery();
		(new RelationshipQueryJoiner($query))->join(['one', 'two']);
		$this->assertEquals($this->trimSql($sql), $query->toSql());
	}

	/** @test */
	public function joinSeveralDoNotOverlap()
	{
		$sql = 'select "stub_models".* from "stub_models"
			left join "stub_model_ones" on "stub_models"."foreign_key" = "stub_model_ones"."id"
			left join "stub_model_threes" on "stub_model_threes"."foreign_key" = "stub_model_ones"."id"';
		$query = (new StubModel)->newQuery();
		(new RelationshipQueryJoiner($query))->join(['one', 'one.five']);
		$this->assertEquals($this->trimSql($sql), $query->toSql());
	}

	protected function trimSql($string)
	{
		return str_replace(["\n", "\t", '  '], [' ', '', ' '], $string);
	}
}

class StubModel extends \Illuminate\Database\Eloquent\Model
{
	public function one()
	{
		return $this->belongsTo(__NAMESPACE__.'\\StubModelOne', 'foreign_key');
	}

	public function two()
	{
		return $this->hasMany(__NAMESPACE__.'\\StubModelTwo', 'foreign_key');
	}

	public function three()
	{
		return $this->belongsToMany(__NAMESPACE__.'\\StubModelThree', 'pivot_table', 'foreign_key', 'local_key');
	}
}

class StubModelOne extends \Illuminate\Database\Eloquent\Model
{
	public function four()
	{
		return $this->belongsTo(__NAMESPACE__.'\\StubModelTwo', 'foreign_key');
	}

	public function five()
	{
		return $this->hasMany(__NAMESPACE__.'\\StubModelThree', 'foreign_key');
	}
}
class StubModelTwo extends \Illuminate\Database\Eloquent\Model {}
class StubModelThree extends \Illuminate\Database\Eloquent\Model {}
