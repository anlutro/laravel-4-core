<?php
namespace anlutro\Core\Tests\Html;

use PHPUnit_Framework_TestCase;

use anlutro\Core\Html\ScriptCollection;

/** @small */
class ScriptCollectionTest extends PHPUnit_Framework_TestCase
{
	/** @test */
	public function canIterate()
	{
		$collection = new ScriptCollection;
		$collection->add('a');
		$collection->add('b');
		$this->assertEquals(['a', 'b'], $collection->all());
	}

	/** @test */
	public function canRemove()
	{
		$collection = new ScriptCollection;
		$collection->add('a');
		$collection->add('b');
		$collection->add('c');
		$collection->remove('b');
		$this->assertEquals(['a', 'c'], $collection->all());
	}

	/** @test */
	public function canAddWithPriority()
	{
		$collection = new ScriptCollection;
		$collection->add('a', 3);
		$collection->add('b', 1);
		$collection->add('c', 2);
		$this->assertEquals(['b', 'c', 'a'], $collection->all());
	}

	/** @test */
	public function foreachAndAllYieldSameResults()
	{
		$collection = new ScriptCollection;
		$collection->add('a', 3);
		$collection->add('b', 1);
		$collection->add('c', 2);
		$found = [];
		foreach ($collection as $key => $value) {
			$found[] = $value;
		}
		$this->assertEquals($found, $collection->all());
	}
}
