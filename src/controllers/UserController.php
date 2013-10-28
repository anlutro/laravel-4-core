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
	public function viewProfile()
	{
		$user = $this->users->getCurrentUser();

		return View::make('c::auth.profile', [
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

		if (!$user->confirmPassword(Input::get('old_password'))) {
			return $this->redirectAction('viewProfile')
				->withErrors(Lang::get('c::auth.invalid-password'));
		}

		$input = Input::except('_token');
		
		// @todo validation
		$validator = Validator::make([], []);

		if ($validator->fails()) {
			return $this->redirectAction('viewProfile')
				->withErrors($validator);
		}

		$redirect = $this->redirectAction('viewProfile');

		if ($this->users->updateProfile($user, $input)) {
			return $redirect->with('success', Lang::get('c::auth.profile-update-success'));
		} else {
			return $redirect->withErrors(Lang::get('c::auth.profile-update-failure'));
		}
	}

	/**
	 * Show a table of users.
	 *
	 * @return View
	 */
	public function userList()
	{
		if (Input::has('search')) {
			$this->users->search(Input::get('search'));
		}

		if (Input::get('usertype')) {
			$this->users->filter(Input::get('usertype'));
		}

		$this->users->togglePagination(20);
		$users = $this->users->getAll();
		$types = ['all' => Lang::get('c::auth.usertype-all')]
			+ $this->users->getUserTypes();

		return View::make('c::user.list', [
			'users'       => $users,
			'userTypes'   => $types,
			'bulkActions' => [
				'-'      => '-',
				'delete' => Lang::get('c::std.delete'),
			],
			'editAction'  => $this->parseAction('showUser'),
			'backUrl'     => URL::to('/'),
			'newUrl'      => $this->urlAction('newUser'),
		]);
	}

	/**
	 * Apply an action on more than one user.
	 *
	 * @return Redirect
	 */
	public function bulkUserAction()
	{
		$userIds = array_keys(Input::get('bulk'));
		$action = Input::get('bulkAction');

		$this->users->processBulkAction($action, $userIds);

		return $this->redirectAction('userList');
	}

	/**
	 * Show a user's info.
	 *
	 * @param  int $userId
	 *
	 * @return View
	 */
	public function showUser($userId)
	{
		if (!$user = $this->users->getByKey($userId))
			return $this->notFoundRedirect();

		$viewData = ['user' => $user];

		$isAdmin = $this->users
			->getCurrentUser()
			->hasAccess('admin');

		if ($isAdmin) {
			$viewData += [
				'backUrl' => $this->urlAction('userList'),
				'editUrl' => $this->urlAction('editUser', [$user->id]),
			];
		} else {
			$viewData += [
				'backUrl' => URL::to('/'),
			];
		}

		return View::make('c::user.show', $data);
	}

	public function editUser($userId)
	{
		if (!$user = $this->users->getByKey($userId))
			return $this->notFoundRedirect();

		return View::make('c::user.form', [
			'pageTitle'  => Lang::get('c::user.edit'),
			'user'       => $user,
			'userTypes'  => $this->getUserTypes(),
			'formAction' => $this->urlAction('updateUser', [$user->id]),
			'deleteUrl'  => $this->urlAction('deleteUser', [$user->id]),
			'backUrl'    => $this->urlAction('userList'),
		]);
	}

	public function updateUser($userId)
	{
		if (!$user = $this->users->getByKey($userId))
			return $this->notFoundRedirect();

		$input = Input::all();

		// @todo validation
		$validator = Validator::make([], []);

		if ($validator->fails()) {
			return $this->redirectAction('editUser', [$user->id])
				->withErrors($validator)
				->withInput();
		}

		// unset the password element if empty to prevent 
		if (empty($input['password'])) {
			unset($input['password']);
		}

		$redirect = $this->redirectAction('showUser', [$user->id]);

		if ($this->users->update($user, $input)) {
			return $redirect->with('success', Lang::get('c::user.update-success'));
		} else {
			return $redirect->withErrors(Lang::get('c::user.update-failure'));
		}
	}

	public function deleteUser($userId)
	{
		if (!$user = $this->users->getByKey($userId))
			return $this->notFoundRedirect();

		if ($this->users->delete($user)) {
			return $this->redirectAction('userList')
				->with('success', Lang::get('c::user.delete-success'));
		} else {
			return $this->redirectAction('showUser', [$user->id])
				->withErrors(Lang::get('c::user.delete-failure'));
		}
	}

	public function newUser()
	{
		return View::make('c::user.form', [
			'pageTitle'  => Lang::get('c::user.new'),
			'user'       => $this->users->getNew(),
			'userTypes'  => $this->getUserTypes(),
			'formAction' => $this->urlAction('createNewUser'),
			'backUrl'    => $this->urlAction('userList'),
		]);
	}

	public function createNewUser()
	{
		$input = Input::all();

		// @todo validation
		$validator = Validator::make([], []);

		if ($validator->fails()) {
			return $this->redirectAction('newUser')
				->withErrors($validator)
				->withInput();
		}

		if ($user = $this->users->create($input)) {
			return $this->redirectAction('showUser', [$user->id])
				->with('success', Lang::get('c::user.create-success'));
		} else {
			return $this->redirectAction('newUser')
				->withErrors(Lang::get('c::user.create-failure'))
				->withInput();
		}
	}

	private function notFoundRedirect()
	{
		return $this->redirectAction('userList')
			->withErrors(Lang::get('c::user.not-found'));
	}

	private function getUserTypes()
	{
		if (!Auth::check() || !Auth::user()->hasAccess('*'))
			return false;

		return $this->users->getUserTypes();
	}
}
