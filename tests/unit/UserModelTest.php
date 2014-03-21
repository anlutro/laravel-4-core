<?php

class UserModelTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider getUserLevelData
	 */
	public function testUserLevels($level, $type, $denied = array())
	{
		$user = $this->makeUserModel();
		$user->user_level = $level;
		$this->assertEquals($type, $user->user_type);
		$this->assertTrue($user->hasAccess($type));

		foreach ($denied as $type) {
			$this->assertFalse($user->hasAccess($type));
		}
	}

	public function getUserLevelData()
	{
		return [
			[1, 'user', ['mod', 'admin', 'superadmin']],
			[2, 'user', ['mod', 'admin', 'superadmin']],
			[9, 'user', ['mod', 'admin', 'superadmin']],
			[10, 'mod', ['admin', 'superadmin']],
			[11, 'mod', ['admin', 'superadmin']],
			[99, 'mod', ['admin', 'superadmin']],
			[100, 'admin', ['superadmin']],
			[101, 'admin', ['superadmin']],
			[254, 'admin', ['superadmin']],
			[255, 'superadmin', []],
		];
	}

	/**
	 * @dataProvider getUserTypeData
	 */
	public function testUserTypes($type, $level)
	{
		$user = $this->makeUserModel();
		$user->user_type = $type;
		$this->assertEquals($level, $user->user_level);
	}

	public function getUserTypeData()
	{
		return [
			['user', 1],
			['mod', 10],
			['admin', 100],
			['superadmin', 255],
		];
	}

	protected function makeUserModel()
	{
		return new anlutro\Core\Auth\UserModel;
	}
}
