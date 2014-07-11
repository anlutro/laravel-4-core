<?php
namespace anlutro\Core\Tests\Web\Controller;

use Mockery as m;
use anlutro\Core\Tests\AppTestCase;

class UserControllerTestCase extends AppTestCase
{
	protected $users;

	public function setUp()
	{
		parent::setUp();
		$this->users = m::mock('anlutro\Core\Auth\UserManager');
		$this->users->shouldReceive('getCurrentUser')->andReturn($this->currentUser = $this->getMockUser())->byDefault();
		$this->currentUser->shouldReceive('hasAccess')->with('*')->andReturn(false)->byDefault();
		$this->app->instance('anlutro\Core\Auth\UserManager', $this->users);
	}

	public function tearDown()
	{
		parent::tearDown();
		m::close();
	}

	protected function setupProfileUpdateExpectations($input, $result)
	{
		$expectation = $this->users->shouldReceive('updateCurrentProfile')->with($input);
		if ($result instanceof \Exception) {
			$expectation->andThrow($result);
		} else {
			$expectation->andReturn($result);
		}
	}

	protected function setupUpdateExpectation($input, $id, $result)
	{
		$user = $this->setupFindExpectations($id);
		$expectation = $this->users->shouldReceive('updateAsAdmin')->once()->with($user, $input);
		if ($result instanceof \Exception) {
			$expectation->andThrow($result);
		} else {
			$expectation->andReturn($result);
		}
		return $user;
	}

	protected function setupIndexExpectations($results = array())
	{
		$this->users->shouldReceive('paginate->getAll')->once()
			->andReturn($results);
	}

	protected function setupFindExpectations($id)
	{
		$mockUser = $this->getMockUser();
		$mockUser->id = $id;
		$this->users->shouldReceive('findByKey')->once()->andReturn($mockUser);
		return $mockUser;
	}

	protected function getMockUser()
	{
		$user = m::mock('anlutro\Core\Auth\Users\UserModel')->makePartial();
		$user->is_active = '1';
		return $user;
	}
}