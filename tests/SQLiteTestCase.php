<?php
class SQLiteTestCase extends PHPunit_Framework_TestCase
{
	public function setUp()
	{
		$this->capsule = new Illuminate\Database\Capsule\Manager;

		$this->capsule->addConnection([
			'driver'   => 'sqlite',
			'database' => ':memory:',
			'prefix'   => '',
		]);

		$this->capsule->setAsGlobal();
		$this->capsule->bootEloquent();
	}

	public function tearDown()
	{
		$this->capsule = null;
	}
}
