<?php
namespace anlutro\Core\Tests\Web\Filters;

use PHPUnit_Framework_TestCase;

use anlutro\Core\Web\Util\ScriptCollection;

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
