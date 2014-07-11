<?php
namespace anlutro\Core\Tests\Web\Controller;

use Mockery as m;

/** @medium */
class UserControllerTest extends UserControllerTestCase
{
	protected $controller = 'anlutro\Core\Web\UserController';

	public function testViewProfile()
	{
		$this->getAction('profile');
		$this->assertResponseOk();
		$this->assertRouteHasFilter('auth');
		$this->assertViewHas('user', $this->currentUser);
	}

	public function testUpdateProfileSuccess()
	{
		$input = ['foo' => 'bar'];
		$this->users->shouldReceive('updateCurrentProfile')
			->with($input)->andReturn(true);

		$this->postAction('updateProfile', [], $input);

		$this->assertRedirectedToAction('profile');
		$this->assertSessionHas('success');
	}

	public function testUpdateProfileFailure()
	{
		$this->setUpProfileUpdateExpectations($input = ['foo' => 'bar'], new \anlutro\LaravelValidation\ValidationException(['baz' => 'bar']));

		$this->postAction('updateProfile', [], $input);

		$this->assertRedirectedToAction('profile');
		$this->assertSessionHasErrors('baz');
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
		$this->users->shouldReceive('findByKey')->andReturn(false);

		$this->getAction('show', [1]);

		$this->assertRedirectedToAction('index');
		$this->assertSessionHasErrors();
	}

	public function testShow()
	{
		$id = 1; $user = $this->setupFindExpectations($id);

		$this->getAction('show', [$id]);

		$this->assertResponseOk();
		$this->assertViewHas('user', $user);
	}

	public function testEdit()
	{
		$id = 1; $user = $this->setupFindExpectations($id);
		$this->users->shouldReceive('getUserTypes')->once()->andReturn([]);
		$this->users->shouldReceive('checkPermissions')->once()->with($user);

		$this->getAction('edit', [$id]);

		$this->assertResponseOk();
		$this->assertViewHas('user', $user);
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
		$this->setupUpdateExpectation($input, $id, new \anlutro\LaravelValidation\ValidationException(['baz' => 'bar']));

		$this->postAction('update', [$id], $input);

		$this->assertRedirectedToAction('edit', [$id]);
		$this->assertSessionHasErrors();
	}

	public function testCreate()
	{
		$this->users->shouldReceive('getNew')->once()
			->andReturn($this->getMockUser());
		$this->users->shouldReceive('getUserTypes')->once()
			->andReturn([]);
		$this->users->shouldReceive('activationsEnabled')->once();

		$this->getAction('create');

		$this->assertResponseOk();
	}

	public function testStoreSuccess()
	{
		$input = ['foo' => 'bar'];
		$user = $this->getMockUser(); $user->id = 1;
		$this->users->shouldReceive('create')->once()
			->with($input)->andReturn($user);

		$this->postAction('store', [], $input);

		$this->assertRedirectedToAction('edit', [$user->id]);
		$this->assertSessionHas('success');
	}

	public function testStoreActivateSuccess()
	{
		$input = ['foo' => 'bar', 'is_active' => '1'];
		$user = $this->getMockUser(); $user->id = 1;
		$this->users->shouldReceive('create')->once()
			->with($input)->andReturn($user);

		$this->postAction('store', [], $input);

		$this->assertRedirectedToAction('edit', [$user->id]);
		$this->assertSessionHas('success');
	}

	public function testStoreFailure()
	{
		$this->users->shouldReceive('create')->once()
			->with($input = ['foo' => 'bar'])->andThrow(new \anlutro\LaravelValidation\ValidationException(['baz']));

		$this->postAction('store', [], $input);

		$this->assertRedirectedToAction('create');
		$this->assertSessionHasErrors();
	}

	protected function setupIndexExpectations($results = array())
	{
		parent::setUpIndexExpectations($results);
		$this->users->shouldReceive('getUserTypes')->once()
			->andReturn([]);
	}
}
