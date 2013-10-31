<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use c\Auth\UserRepository;

class UserController extends anlutro\L4Base\Controller
{
	protected $users;

	public function __construct(UserRepository $users)
	{
		$this->users = $users;
	}

	/**
	 * View the logged in user's profile.
	 *
	 * @return View
	 */
	public function profile()
	{
		$user = $this->users->getCurrentUser();

		$url = URL::action('UserController@profile');

		return View::make('c::user.profile', [
			'user' => $user,
			'formAction' => $this->urlAction('updateProfile'),
			'backUrl' => URL::to('/'),
		]);
	}

	/**
	 * Update the logged in user's profile.
	 *
	 * @return Redirect
	 */
	public function updateProfile()
	{
		$user = $this->users->getCurrentUser();
		$redirect = $this->redirectAction('profile');

		if (!$user->confirmPassword(Input::get('old_password'))) {
			return $redirect->withErrors(Lang::get('c::auth.invalid-password'));
		}

		$input = Input::all();

		if ($this->users->updateProfile($user, $input)) {
			return $redirect->with('success', Lang::get('c::user.profile-update-success'));
		} else {
			return $redirect->withErrors($this->users->errors());
		}
	}

	/**
	 * Show a table of users.
	 *
	 * @return View
	 */
	public function index()
	{
		if (Input::has('search')) {
			$this->users->search(Input::get('search'));
		}

		if (Input::get('usertype')) {
			$this->users->filter(Input::get('usertype'));
		}

		$this->users->togglePagination(20);
		$users = $this->users->getAll();
		$types = ['all' => Lang::get('c::user.usertype-all')]
			+ $this->users->getUserTypes();

		return View::make('c::user.list', [
			'users'       => $users,
			'userTypes'   => $types,
			'bulkActions' => [
				'-'      => '-',
				'delete' => Lang::get('c::std.delete'),
			],
			'editAction'  => $this->parseAction('edit'),
			'newUrl'      => $this->urlAction('create'),
			'backUrl'     => URL::to('/'),
		]);
	}

	/**
	 * Apply an action on more than one user.
	 *
	 * @return Redirect
	 */
	public function bulk()
	{
		$userIds = array_keys(Input::get('bulk'));
		$action = Input::get('bulkAction');

		$this->users->processBulkAction($action, $userIds);

		return $this->redirectAction('index');
	}

	/**
	 * Show a user's info.
	 *
	 * @param  int $userId
	 *
	 * @return View
	 */
	public function show($userId)
	{
		if (!$user = $this->users->getByKey($userId))
			return $this->notFoundRedirect();

		$viewData = [
			'user' => $user,
			'backUrl' => URL::to('/'),
		];

		$isAdmin = $this->users
			->getCurrentUser()
			->hasAccess('admin');

		if ($isAdmin) {
			$viewData['editUrl'] = $this->urlAction('edit', [$user->id]);
		}

		return View::make('c::user.show', $viewData);
	}

	public function edit($userId)
	{
		if (!$user = $this->users->getByKey($userId))
			return $this->notFoundRedirect();

		return View::make('c::user.form', [
			'pageTitle'  => Lang::get('c::user.admin-edituser'),
			'user'       => $user,
			'userTypes'  => $this->getUserTypes(),
			'formAction' => $this->urlAction('update', [$user->id]),
			'deleteUrl'  => $this->urlAction('delete', [$user->id]),
			'backUrl'    => $this->urlAction('index'),
		]);
	}

	public function update($userId)
	{
		if (!$user = $this->users->getByKey($userId))
			return $this->notFoundRedirect();

		$input = Input::all();
		$redirect = $this->redirectAction('edit', [$user->id]);

		if ($this->users->update($user, $input)) {
			return $redirect->with('success', Lang::get('c::user.update-success'));
		} else {
			return $redirect->withErrors($this->users->errors());
		}
	}

	public function delete($userId)
	{
		if (!$user = $this->users->getByKey($userId))
			return $this->notFoundRedirect();

		if ($this->users->delete($user)) {
			return $this->redirectAction('index')
				->with('success', Lang::get('c::user.delete-success'));
		} else {
			return $this->redirectAction('edit', [$user->id])
				->withErrors(Lang::get('c::user.delete-failure'));
		}
	}

	public function create()
	{
		return View::make('c::user.form', [
			'pageTitle'  => Lang::get('c::user.admin-newuser'),
			'user'       => $this->users->getNew(),
			'userTypes'  => $this->getUserTypes(),
			'formAction' => $this->urlAction('store'),
			'backUrl'    => $this->urlAction('index'),
		]);
	}

	public function store()
	{
		$input = Input::all();

		if ($user = $this->users->create($input)) {
			return $this->redirectAction('edit', [$user->id])
				->with('success', Lang::get('c::user.create-success'));
		} else {
			return $this->redirectAction('create')
				->withErrors($this->users->errors())
				->withInput();
		}
	}

	private function notFoundRedirect()
	{
		return $this->redirectAction('index')
			->withErrors(Lang::get('c::user.not-found'));
	}

	private function getUserTypes()
	{
		if (!Auth::check() || !Auth::user()->hasAccess('*'))
			return false;

		return $this->users->getUserTypes();
	}
}
