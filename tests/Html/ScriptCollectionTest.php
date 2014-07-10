<?php
namespace anlutro\Core\Tests\Html;

use PHPUnit_Framework_TestCase;

use anlutro\Core\Html\ScriptCollection;

/** @small */
class ScriptCollectionTest extends PHPUnit_Framework_TestCase
{
	/** @test */
	public function invalidUrlArgumentThrowsException()
	{
		$collection = new ScriptCollection;
		$this->setExpectedException('InvalidArgumentException');
		$collection->add(1);
	}

	/** @test */
	public function invalidPriorityArgumentThrowsException()
	{
		$collection = new ScriptCollection;
		$this->setExpectedException('InvalidArgumentException');
		$collection->add('foo', 'bar');
	}

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
		$collection->add('a', 1);
		$collection->add('b', 1);
		$collection->add('c', 3);
		$collection->add('d', 2);
		$this->assertEquals(['c', 'd', 'a', 'b'], $collection->all());
	}

	/** @test */
	public function foreachAndAllYieldSameResults()
	{
		$collection = new ScriptCollection;
		$collection->add('a', 1);
		$collection->add('b', 1);
		$collection->add('c', 3);
		$collection->add('d', 2);
		$found = [];
		foreach ($collection as $key => $value) {
			$found[] = $value;
		}
		$this->assertEquals($found, $collection->all());
	}

	/** @test */
	public function canAddDebugUrls()
	{
		$collection = new ScriptCollection(false);
		$collection->add(['foo.css', 'foo.min.css']);
		$this->assertEquals(['foo.css'], $collection->all());
		$collection->setDebug(true);
		$this->assertEquals(['foo.min.css'], $collection->all());
	}
}
