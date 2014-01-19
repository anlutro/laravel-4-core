<?php
use Mockery as m;

class UserControllerTest extends AppTestCase
{
	protected $controller = 'UserController';

	public function setUp()
	{
		parent::setUp();
		$this->users = m::mock('c\Auth\UserRepository');
		$this->app->instance('c\Auth\UserRepository', $this->users);
	}

	public function tearDown()
	{
		m::close();
	}

	public function testViewProfile()
	{
		$curUser = $this->expectCurrentUser();

		$this->getAction('profile');
		$this->assertResponseOk();
		$this->assertRouteHasFilter('auth');
	}

	public function testUpdateProfileWithIncorrectPassword()
	{
		$input = ['old_password' => 'foo'];
		$curUser = $this->expectCurrentUser();
		$curUser->shouldReceive('confirmPassword')
			->with($input['old_password'])
			->andReturn(false);

		$this->postAction('updateProfile', [], $input);

		$this->assertRedirectedToAction('profile');
		$this->assertSessionHasErrors();
	}

	protected function setUpProfileUpdateExpectations($input, $result)
	{
		$curUser = $this->expectCurrentUser();
		$curUser->shouldReceive('confirmPassword')
			->andReturn(true);
		$this->users->shouldReceive('update')
			->with($curUser, $input)
			->andReturn($result);
	}

	public function testUpdateProfileSuccess()
	{
		$input = ['foo' => 'bar'];
		$this->setUpProfileUpdateExpectations($input, true);

		$this->postAction('updateProfile', [], $input);

		$this->assertRedirectedToAction('profile');
		$this->assertSessionHas('success');
	}

	public function testUpdateProfileFailure()
	{
		$input = ['foo' => 'bar'];
		$this->setUpProfileUpdateExpectations($input, false);
		$this->users->shouldReceive('errors')
			->andReturn(['baz' => 'bar']);

		$this->postAction('updateProfile', [], $input);

		$this->assertRedirectedToAction('profile');
		$this->assertSessionHasErrors('baz');
	}

	protected function setUpIndexExpectations()
	{
		$this->users->shouldReceive('getUserTypes')->once()
			->andReturn([]);
		$this->users->shouldReceive('paginate->getAll')->once()
			->andReturn([]);
	}

	public function testIndex()
	{
		$this->setUpIndexExpectations();

		$this->getAction('index');

		$this->assertResponseOk();
		$this->assertRouteHasFilter('auth');
		$this->assertRouteHasFilter('access:admin');
		$this->assertViewHas('users');
	}

	public function testIndexWithSearch()
	{
		$this->setUpIndexExpectations();
		$this->users->shouldReceive('search')
			->with('foo');

		$this->getAction('index', ['search' => 'foo']);

		$this->assertResponseOk();
	}

	public function testIndexWithFilter()
	{
		$this->setUpIndexExpectations();
		$this->users->shouldReceive('filter')
			->with('bar');

		$this->getAction('index', ['usertype' => 'bar']);

		$this->assertResponseOk();
	}

	public function testBulkAction()
	{
		$ids = [1 => 'checked', 2 => 'checked', 3 => 'checked'];
		$input = ['bulkAction' => 'delete', 'bulk' => $ids];
		$this->users->shouldReceive('processBulkAction')->once()
			->with($input['bulkAction'], array_keys($ids));

		$this->postAction('bulk', [], $input);

		$this->assertRedirectedToAction('index');
	}

	public function testNotFound()
	{
		$this->users->shouldReceive('getByKey')->andReturn(false);

		$this->getAction('show', [1]);

		$this->assertRedirectedToAction('index');
		$this->assertSessionHasErrors();
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
		$curUser = $this->expectCurrentUser();
		$curUser->shouldReceive('hasAccess')->with('*')->once()->andReturn(true);

		$this->getAction('show', [$id]);

		$this->assertResponseOk();
		$this->assertViewHas('user', $user);
		$this->assertViewHas('editUrl');
	}

	public function testEdit()
	{
		$id = 1; $user = $this->expectFindUser($id);
		$this->users->shouldReceive('getUserTypes')->andReturn([]);

		$this->getAction('edit', [$id]);

		$this->assertResponseOk();
		$this->assertViewHas('user', $user);
	}

	protected function setupUpdateExpectation($input, $id, $result)
	{
		$user = $this->expectFindUser($id);
		$this->users->shouldReceive('updateAsAdmin')->once()
			->with($user, $input)
			->andReturn($result);
		return $user;
	}

	public function testUpdateSuccess()
	{
		$input = ['foo' => 'bar']; $id = 1;
		$this->setupUpdateExpectation($input, $id, true);

		$this->postAction('update', [$id], $input);

		$this->assertRedirectedToAction('edit', [$id]);
		$this->assertSessionHas('success');
	}

	public function testUpdateFailure()
	{
		$input = ['foo' => 'bar']; $id = 1;
		$this->setupUpdateExpectation($input, $id, false);
		$this->users->shouldReceive('errors')
			->andReturn(['baz' => 'bar']);

		$this->postAction('update', [$id], $input);

		$this->assertRedirectedToAction('edit', [$id]);
		$this->assertSessionHasErrors();
	}

	public function testCreate()
	{
		$this->users->shouldReceive('getNew')->once()
			->andReturn($this->getMockUser());

		$this->getAction('create');

		$this->assertResponseOk();
	}

	public function testStoreSuccess()
	{
		$input = ['foo' => 'bar'];
		$user = $this->getMockUser(); $user->id = 1;
		$this->users->shouldReceive('create')->once()
			->with($input, true)->andReturn($user);

		$this->postAction('store', [], $input);

		$this->assertRedirectedToAction('edit', [$user->id]);
		$this->assertSessionHas('success');
	}

	public function testStoreActivateSuccess()
	{
		$input = ['foo' => 'bar', 'activate' => '1'];
		$user = $this->getMockUser(); $user->id = 1;
		$this->users->shouldReceive('create')->once()
			->with($input, true)->andReturn($user);

		$this->postAction('store', [], $input);

		$this->assertRedirectedToAction('edit', [$user->id]);
		$this->assertSessionHas('success');
	}

	public function testStoreFailure()
	{
		$input = ['foo' => 'bar'];
		$this->users->shouldReceive('create')->once()
			->with($input, m::any())->andReturn(false);
		$this->users->shouldReceive('errors')->once()
			->andReturn('baz');

		$this->postAction('store', [], $input);

		$this->assertRedirectedToAction('create');
		$this->assertSessionHasErrors();
	}

	protected function expectCurrentUser()
	{
		$mockUser = $this->getMockUser();
		$this->users->shouldReceive('getCurrentUser')->andReturn($mockUser);
		return $mockUser;
	}

	protected function getMockUser()
	{
		$user = m::mock('c\Auth\UserModel')->makePartial();
		$user->is_active = '1';
		return $user;
	}
}
