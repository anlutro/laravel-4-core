<?php
/**
 * Laravel 4 Core - User Controller
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use c\Auth\UserRepository;

/**
 * Controller for managing users, not including authentication.
 */
class UserController extends \c\Controller
{
	/**
	 * @var c\Auth\UserRepository
	 */
	protected $users;

	/**
	 * @param UserRepository $users
	 */
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
			'user'       => $user,
			'formAction' => $this->url('updateProfile'),
			'backUrl'    => URL::to('/'),
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
		$redirect = $this->redirect('profile');

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

		$users = $this->users
			->paginate(20)
			->getAll();
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
			'newUrl'      => $this->url('create'),
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

		return $this->redirect('index');
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
		if (!$user = $this->users->getByKey($userId)) {
			return $this->notFoundRedirect();
		}

		$viewData = [
			'user' => $user,
			'backUrl' => URL::to('/'),
		];

		$isAdmin = $this->users
			->getCurrentUser()
			->hasAccess('*');

		if ($isAdmin) {
			$viewData['editUrl'] = $this->url('edit', [$user->id]);
		}

		return View::make('c::user.show', $viewData);
	}

	/**
	 * Show the edit form for a user.
	 *
	 * @param  int $userId
	 *
	 * @return View
	 */
	public function edit($userId)
	{
		if (!$user = $this->users->getByKey($userId)) {
			return $this->notFoundRedirect();
		}

		return View::make('c::user.form', [
			'pageTitle'  => Lang::get('c::user.admin-edituser'),
			'user'       => $user,
			'isActive'   => (bool) $user->is_active,
			'userTypes'  => $this->getUserTypes(),
			'formAction' => $this->url('update', [$user->id]),
			'deleteUrl'  => $this->url('delete', [$user->id]),
			'backUrl'    => $this->url('index'),
		]);
	}

	/**
	 * Update a user's information.
	 *
	 * @param  int $userId
	 *
	 * @return Redirect
	 */
	public function update($userId)
	{
		if (!$user = $this->users->getByKey($userId)) {
			return $this->notFoundRedirect();
		}

		$input = Input::all();
		$redirect = $this->redirect('edit', [$user->id]);

		if ($this->users->update($user, $input)) {
			return $redirect->with('success', Lang::get('c::user.update-success'));
		} else {
			return $redirect->withErrors($this->users->errors());
		}
	}

	/**
	 * Delete an existing user.
	 *
	 * @param  int $userId
	 *
	 * @return Redirect
	 */
	public function delete($userId)
	{
		if (!$user = $this->users->getByKey($userId)) {
			return $this->notFoundRedirect();
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
	 * @return View
	 */
	public function create()
	{
		return View::make('c::user.form', [
			'pageTitle'  => Lang::get('c::user.admin-newuser'),
			'user'       => $this->users->newInstance(),
			'userTypes'  => $this->getUserTypes(),
			'formAction' => $this->url('store'),
			'backUrl'    => $this->url('index'),
		]);
	}

	/**
	 * Store a new user in the database.
	 *
	 * @return Redirect
	 */
	public function store()
	{
		$input = Input::all();
		$activate = $this->activationEnabled() ? Input::has('activate') : true;

		if ($user = $this->users->create($input, $activate)) {
			return $this->redirect('edit', [$user->id])
				->with('success', Lang::get('c::user.create-success'));
		} else {
			return $this->redirect('create')
				->withErrors($this->users->errors())
				->withInput();
		}
	}

	/**
	 * Return a not found redirect.
	 *
	 * @return Redirect
	 */
	private function notFoundRedirect()
	{
		return $this->redirect('index')
			->withErrors(Lang::get('c::user.not-found'));
	}

	/**
	 * Get a list of user types. Return false if not logged in or not allowed
	 * to edit user types.
	 * 
	 * @return array|false
	 */
	private function getUserTypes()
	{
		if (!Auth::check() || !Auth::user()->hasAccess('admin')) {
			return false;
		}

		return $this->users->getUserTypes();
	}

	/**
	 * Check if user activation is enabled.
	 *
	 * @return boolean
	 */
	private function activationEnabled()
	{
		$loaded = App::getLoadedProviders();
		$provider = 'c\Auth\Activation\ActivationServiceProvider';
		return isset($loaded[$provider]);
	}
}
