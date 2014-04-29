<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Web;

use anlutro\LaravelController\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

use anlutro\Core\Auth\UserManager;

/**
 * Controller for managing users, not including authentication.
 */
class UserController extends Controller
{
	/**
	 * @var \anlutro\Core\Auth\UserManager
	 */
	protected $users;

	/**
	 * @param \anlutro\Core\Auth\UserManager $users
	 */
	public function __construct(UserManager $users)
	{
		$this->users = $users;
	}

	/**
	 * View the logged in user's profile.
	 *
	 * @return \Illuminate\View\View
	 */
	public function profile()
	{
		$user = $this->users->getCurrentUser();

		return $this->view('c::user.profile', [
			'user'       => $user,
			'formAction' => $this->url('updateProfile'),
			'backUrl'    => URL::to('/'),
		]);
	}

	/**
	 * Update the logged in user's profile.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function updateProfile()
	{
		$redirect = $this->redirect('profile');

		if ($this->users->updateCurrentProfile($this->input())) {
			return $redirect->with('success', Lang::get('c::user.profile-update-success'));
		} else {
			return $redirect->withErrors($this->users->getErrors());
		}
	}

	/**
	 * Show a table of users.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		if ($this->input('search')) {
			$this->users->search($this->input('search'));
		}

		if ($this->input('usertype')) {
			$this->users->filter($this->input('usertype'));
		}

		$users = $this->users
			->paginate(20)
			->getAll();
		$types = ['all' => Lang::get('c::user.usertype-all')]
			+ $this->getUserTypes();

		return $this->view('c::user.list', [
			'users'       => $users,
			'userTypes'   => $types,
			'bulkActions' => $this->getBulkActions(),
			'editAction'  => $this->action('edit'),
			'newUrl'      => $this->url('create'),
			'backUrl'     => URL::to('/'),
		]);
	}

	/**
	 * Apply an action on more than one user.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function bulk()
	{
		$userIds = array_keys($this->input('bulk'));
		$action = $this->input('bulkAction');

		$this->users->processBulkAction($action, $userIds);

		return $this->redirect('index');
	}

	/**
	 * Show a user's info.
	 *
	 * @param  int $userId
	 *
	 * @return \Illuminate\View\View
	 */
	public function show($userId)
	{
		if (!$user = $this->users->getByKey($userId)) {
			return $this->notFound();
		}

		return $this->view('c::user.show', [
			'user'    => $user,
			'backUrl' => URL::to('/'),
		]);
	}

	/**
	 * Show the edit form for a user.
	 *
	 * @param  int $userId
	 *
	 * @return \Illuminate\View\View
	 */
	public function edit($userId)
	{
		if (!$user = $this->users->getByKey($userId)) {
			return $this->notFound();
		}

		$this->users->checkPermissions($user);

		$viewData = [
			'pageTitle'  => Lang::get('c::user.admin-edituser'),
			'user'       => $user,
			'isActive'   => (bool) $user->is_active,
			'userTypes'  => $this->getUserTypes(),
			'formAction' => $this->url('update', [$user->id]),
			'deleteUrl'  => $this->url('delete', [$user->id]),
			'backUrl'    => $this->url('index'),
		];

		return $this->view('c::user.form', $viewData);
	}

	/**
	 * Update a user's information.
	 *
	 * @param  int $userId
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($userId)
	{
		if (!$user = $this->users->getByKey($userId)) {
			return $this->notFound();
		}

		$redirect = $this->redirect('edit', [$user->id]);

		if ($this->users->updateAsAdmin($user, $this->input())) {
			return $redirect->with('success', Lang::get('c::user.update-success'));
		} else {
			return $redirect->withErrors($this->users->getErrors());
		}
	}

	/**
	 * Delete an existing user.
	 *
	 * @param  int $userId
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($userId)
	{
		if (!$user = $this->users->getByKey($userId)) {
			return $this->notFound();
		}

		if ($this->users->delete($user)) {
			return $this->redirect('index')
				->with('success', Lang::get('c::user.delete-success'));
		} else {
			return $this->redirect('edit', [$user->id])
				->withErrors(Lang::get('c::user.delete-failure'));
		}
	}

	/**
	 * Show the create new user form.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return $this->view('c::user.form', [
			'pageTitle'  => Lang::get('c::user.admin-newuser'),
			'user'       => $this->users->getNew(),
			'userTypes'  => $this->getUserTypes(),
			'formAction' => $this->url('store'),
			'backUrl'    => $this->url('index'),
		]);
	}

	/**
	 * Store a new user in the database.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		$input = $this->input();

		if ($user = $this->users->create($input)) {
			return $this->redirect('edit', [$user->id])
				->with('success', Lang::get('c::user.create-success'));
		} else {
			return $this->redirect('create')
				->withErrors($this->users->getErrors())
				->withInput();
		}
	}

	/**
	 * Return a not found redirect.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	private function notFound()
	{
		return $this->redirect('index')
			->withErrors(Lang::get('c::user.not-found'));
	}

	/**
	 * Get a list of user types.
	 * 
	 * @return array|false
	 */
	private function getUserTypes()
	{
		$types = $this->users->getUserTypes();
		$strings = [];

		foreach ($types as $type) {
			if (!empty($type)) {
				$strings[$type] = Lang::get('c::user.usertype-'.$type);
			}
		}

		return $strings;
	}

	private function getBulkActions()
	{
		return [
			'-'      => '-',
			'delete' => Lang::get('c::std.delete'),
		];
	}
}
