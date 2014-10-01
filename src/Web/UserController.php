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
use anlutro\LaravelValidation\ValidationException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use anlutro\Core\Auth\UserManager;
use anlutro\Core\Auth\AccessDeniedException;

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

		if ($this->users->getCurrentUser()->hasAccess('*')) {
			$this->users->withSoftDeleted();
		}
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

		try {
			$this->users->updateCurrentProfile($this->input());
			return $redirect->with('success', Lang::get('c::user.profile-update-success'));
		} catch (ValidationException $e) {
			return $redirect->withErrors($e);
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

		$types = ['' => Lang::get('c::user.usertypes-all')]
			+ $this->getUserTypes();

		return $this->view('c::user.list', [
			'users'       => $users,
			'userTypes'   => $types,
			'bulkActions' => $this->getBulkActions(),
			'showAction'  => $this->action('show'),
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
		$redirect = $this->redirect('index');
		$input = $this->input('bulk');
		$action = $this->input('bulkAction');

		if (empty($input)) {
			return $redirect->withInput()
				->with('error', Lang::get('c::std.none-selected', ['model' => Lang::get('c::user.model-user')]));
		}

		if (empty($action)) {
			return $redirect->withInput()
				->with('error', Lang::get('c::std.invalid-action'));
		}

		$this->users->processBulkAction($action, $input);

		return $redirect;
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
		if (!$user = $this->users->findByKey($userId)) {
			return $this->notFound();
		}

		$canEdit = $this->users->hasPermission($user);

		$view = $this->view('c::user.show', [
			'user'    => $user,
			'backUrl' => URL::to('/'),
			'canEdit' => $canEdit,
		]);

		if ($canEdit) {
			$view->with([
				'editUrl' => $this->url('@edit', [$userId]),
			]);
		}

		return $view;
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
		if (!$user = $this->users->findByKey($userId)) {
			return $this->notFound();
		}

		try {
			$this->users->checkPermissions($user);
		} catch (AccessDeniedException $e) {
			$this->addWarningMessage(Lang::get('c::auth.access-denied'));
		}

		$viewData = [
			'pageTitle'  => Lang::get('c::user.admin-edituser'),
			'user'       => $user,
			'isActive'   => (bool) $user->is_active,
			'userTypes'  => $this->getUserTypes(),
			'formAction' => $this->url('update', [$user->id]),
			'deleteUrl'  => $this->url('delete', [$user->id]),
			'restoreUrl' => $user->deleted_at ? $this->url('restore', [$user->id]) : null,
			'backUrl'    => $this->url('index'),
		];

		return $this->view('c::user.form', $viewData);
	}

	protected function addWarningMessage($message)
	{
		if (!Session::has('errors') || Session::get('errors')->isEmpty()) {
			Session::put('warning', $message);
			Session::push('flash.old', 'warning');
		}
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
		if (!$user = $this->users->findByKey($userId)) {
			return $this->notFound();
		}

		$redirect = $this->redirect('edit', [$user->id]);

		try {
			$this->users->updateAsAdmin($user, $this->input());
			return $redirect->with('success', Lang::get('c::user.update-success'));
		} catch (AccessDeniedException $e) {
			return $redirect->with('error', Lang::get('c::auth.access-denied'));
		} catch (ValidationException $e) {
			return $redirect->withErrors($e);
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
		if (!$user = $this->users->findByKey($userId)) {
			return $this->notFound();
		}

		try {
			$this->users->delete($user);

			return $this->redirect('index')
				->with('success', Lang::get('c::user.delete-success'));
		} catch (AccessDeniedException $e) {
			return $this->redirect('edit', [$user->id])
				->with('error', Lang::get('c::auth.access-denied'));
		}
	}

	/**
	 * Restore a soft deleted user.
	 *
	 * @param  int $userId
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function restore($userId)
	{
		if (!$user = $this->users->findByKey($userId)) {
			return $this->notFound();
		}

		$this->users->restore($user);

		return $this->redirect('edit', [$user->id])
			->with('success', Lang::get('c::user.restore-success'));
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
			'activate'   => $this->users->activationsEnabled(),
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

		try {
			$user = $this->users->create($input);
			return $this->redirect('edit', [$user->id])
				->with('success', Lang::get('c::user.create-success'));
		} catch (ValidationException $e) {
			return $this->redirect('create')
				->withErrors($e)
				->withInput();
		}
	}

	/**
	 * Return a not found redirect.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function notFound()
	{
		return $this->redirect('index')
			->with('error', Lang::get('c::std.not-found', ['model' => Lang::get('c::user.model-user')]));
	}

	/**
	 * Get a list of user types.
	 * 
	 * @return array|false
	 */
	protected function getUserTypes()
	{
		$types = $this->users->getUserTypes();
		$strings = [];

		foreach ($types as $type) {
			if (!empty($type)) {
				$strings[$type] = Lang::get('c::user.usertypes-'.$type);
			}
		}

		return $strings;
	}

	protected function getBulkActions()
	{
		return [
			'-'          => '-',
			'delete'     => Lang::get('c::std.delete'),
			'restore'    => Lang::get('c::std.restore'),
			'activate'   => Lang::get('c::user.activate'),
			'deactivate' => Lang::get('c::user.deactivate'),
		];
	}
}
