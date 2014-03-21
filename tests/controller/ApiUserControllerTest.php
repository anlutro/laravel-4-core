<?php
use Mockery as m;

class ApiUserControllerTest extends AppTestCase
{
	protected $controller = 'anlutro\Core\Web\UserController';

	public function setUp()
	{
		parent::setUp();
		$this->users = m::mock('anlutro\Core\Auth\UserManager');
		$this->app->instance('anlutro\Core\Auth\UserManager', $this->users);

		// instead of setting up the filters and having to deal with auth/csrf, just
		// bind the controllers manually
		$this->app->bind('anlutro\Core\Web\AuthController', 'anlutro\Core\Web\ApiAuthController');
		$this->app->bind('anlutro\Core\Web\UserController', 'anlutro\Core\Web\ApiUserController');

		// mock a JSON/AJAX request
		$this->client->setServerParameter('HTTP_X-Requested-With', 'XMLHttpRequest');
		$this->client->setServerParameter('HTTP_CONTENT_TYPE', 'application/json');
		$this->client->setServerParameter('HTTP_ACCEPT', 'application/json');
	}

	public function tearDown()
	{
		m::close();
	}

	public function assertResponseJson($response)
	{
		$this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
		return $response->getData();
	}

	public function assertResponse200($response)
	{
		$actual = $response->getStatusCode();
		$this->assertTrue($response->isOk(), 'Expected status code 200, got '.$actual);
	}

	protected function setUpProfileUpdateExpectations($input, $result)
	{
		$this->users->shouldReceive('updateCurrentProfile')
			->with($input)->andReturn($result);
	}

	protected function setupUpdateExpectation($input, $id, $result)
	{
		$user = $this->expectFindUser($id);
		$this->users->shouldReceive('updateAsAdmin')->once()
			->with($user, $input)
			->andReturn($result);
		return $user;
	}

	public function testViewProfile()
	{
		$user = $this->getMockUser(); $user->name = 'foo';
		$this->users->shouldReceive('getCurrentUser')->andReturn($user);

		$response = $this->getAction('profile');
		$this->assertResponse200($response);
		$data = $this->assertResponseJson($response);
		$this->assertEquals('foo', $data->user->name);
	}

	public function testUpdateProfileSuccess()
	{
		$input = ['foo' => 'bar'];
		$this->users->shouldReceive('updateCurrentProfile')
			->with($input)->andReturn(true);
		$user = $this->getMockUser(); $user->name = 'foo';
		$this->users->shouldReceive('getCurrentUser')->andReturn($user);

		$response = $this->postAction('updateProfile', [], $input);
		$this->assertResponse200($response);
		$data = $this->assertResponseJson($response);
		$this->assertEquals('foo', $data->user->name);
	}

	public function testUpdateProfileFailure()
	{
		$input = ['foo' => 'bar'];
		$this->users->shouldReceive('updateCurrentProfile')
			->with($input)->andReturn(false);
		$this->users->shouldReceive('getErrors')
			->andReturn(['baz' => 'bar']);

		$response = $this->postAction('updateProfile', [], $input);
		$data = $this->assertResponseJson($response);
		$this->assertEquals(400, $response->getStatusCode());
		$this->assertEquals('bar', $data->errors->baz);
	}

	protected function setUpIndexExpectations($results = array())
	{
		$this->users->shouldReceive('paginate->getAll')->once()
			->andReturn(m::mock(['toArray' => $results]));
	}

	public function testIndex()
	{
		$this->setUpIndexExpectations('foo');

		$response = $this->getAction('index');

		$this->assertRouteHasFilter('auth');
		$this->assertRouteHasFilter('access:admin');
		$data = $this->assertResponseJson($response);
		$this->assertResponse200($response);
		$this->assertEquals('foo', $data->users);
	}

	public function testIndexWithSearch()
	{
		$this->setUpIndexExpectations('foo');
		$this->users->shouldReceive('search')
			->with('foo');

		$response = $this->getAction('index', ['search' => 'foo']);

		$this->assertRouteHasFilter('auth');
		$this->assertRouteHasFilter('access:admin');
		$data = $this->assertResponseJson($response);
		$this->assertResponse200($response);
		$this->assertEquals('foo', $data->users);
	}

	public function testIndexWithFilter()
	{
		$this->setUpIndexExpectations('foo');
		$this->users->shouldReceive('filter')
			->with('bar');

		$response = $this->getAction('index', ['usertype' => 'bar']);

		$this->assertRouteHasFilter('auth');
		$this->assertRouteHasFilter('access:admin');
		$data = $this->assertResponseJson($response);
		$this->assertResponse200($response);
		$this->assertEquals('foo', $data->users);
	}

	public function testBulkAction()
	{
		$ids = [2, 4, 6];
		$input = ['bulkAction' => 'delete', 'bulk' => $ids];
		$this->users->shouldReceive('processBulkAction')->once()
			->with($input['bulkAction'], $ids)->andReturn(3);

		$response = $this->postAction('bulk', [], $input);

		$data = $this->assertResponseJson($response);
		$this->assertResponse200($response);
		$this->assertEquals('3 affected rows', $data->messages[0]);
	}

	public function testNotFound()
	{
		$this->users->shouldReceive('getByKey')->andReturn(false);

		$response = $this->getAction('show', [1]);

		$this->assertResponseJson($response);
		$this->assertEquals(404, $response->getStatusCode());
	}

	protected function expectFindUser($id)
	{
		$mockUser = $this->getMockUser();
		$mockUser->id = $id;
		$this->users->shouldReceive('getByKey')->once()->andReturn($mockUser);
		return $mockUser;
	}

	public function testShow()
	{
		$id = 1; $user = $this->expectFindUser($id);

		$response = $this->getAction('show', [$id]);
		
		$data = $this->assertResponseJson($response);
		$this->assertResponse200($response);
		$this->assertEquals(1, $data->user->id);
	}

	public function testUpdateSuccess()
	{
		$input = ['foo' => 'bar']; $id = 1;
		$this->setupUpdateExpectation($input, $id, true);

		$response = $this->postAction('update', [$id], $input);
		
		$data = $this->assertResponseJson($response);
		$this->assertResponse200($response);
		$this->assertEquals(1, $data->user->id);
	}

	public function testUpdateFailure()
	{
		$input = ['foo' => 'bar']; $id = 1;
		$this->setupUpdateExpectation($input, $id, false);
		$this->users->shouldReceive('getErrors')
			->andReturn(['baz' => 'bar']);

		$response = $this->postAction('update', [$id], $input);

		$data = $this->assertResponseJson($response);
		$this->assertEquals(400, $response->getStatusCode());
		$this->assertEquals('bar', $data->errors->baz);
	}

	public function testStoreSuccess()
	{
		$input = ['foo' => 'bar'];
		$user = $this->getMockUser(); $user->id = 1;
		$this->users->shouldReceive('create')->once()
			->with($input)->andReturn($user);

		$response = $this->postAction('store', [], $input);
		
		$data = $this->assertResponseJson($response);
		$this->assertResponse200($response);
		$this->assertEquals(1, $data->user->id);
	}

	public function testStoreFailure()
	{
		$input = ['foo' => 'bar'];
		$this->users->shouldReceive('create')->once()
			->with($input)->andReturn(false);
		$this->users->shouldReceive('getErrors')->once()
			->andReturn(['baz' => 'bar']);

		$response = $this->postAction('store', [], $input);

		$data = $this->assertResponseJson($response);
		$this->assertEquals(400, $response->getStatusCode());
		$this->assertEquals('bar', $data->errors->baz);
	}

	protected function getMockUser()
	{
		$user = m::mock('anlutro\Core\Auth\UserModel')->makePartial();
		$user->is_active = '1';
		return $user;
	}
}
