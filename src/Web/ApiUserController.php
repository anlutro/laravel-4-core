<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Web;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

use anlutro\Core\Auth\UserManager;

/**
 * Controller for managing users, not including authentication.
 */
class ApiUserController extends \anlutro\LaravelController\ApiController
{
	/**
	 * @var anlutro\Core\Auth\UserRepository
	 */
	protected $users;

	/**
	 * @param UserRepository $users
	 */
	public function __construct(UserManager $users)
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

		return Response::json(['user' => $user]);
	}

	/**
	 * Update the logged in user's profile.
	 *
	 * @return Redirect
	 */
	public function updateProfile()
	{
		if ($this->users->updateCurrentProfile(Input::all())) {
			return Response::json(['user' => $this->users->getCurrentUser()]);
		} else {
			return $this->error($this->users->getErrors());
		}
	}

	/**
	 * Show a table of users.
	 *
	 * @return View
	 */
	public function index()
	{
		if (Input::get('search')) {
			$this->users->search(Input::get('search'));
		}

		if (Input::get('usertype')) {
			$this->users->filter(Input::get('usertype'));
		}

		$users = $this->users
			->paginate(20)
			->getAll();

		return Response::json(['users' => $users->toArray()]);
	}

	/**
	 * Apply an action on more than one user.
	 *
	 * @return Redirect
	 */
	public function bulk()
	{
		$userIds = Input::get('bulk');
		$action = Input::get('bulkAction');

		$result = $this->users->processBulkAction($action, $userIds);

		return $this->success("$result affected rows");
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
			return $this->notFound();
		} else{
			return Response::json(['user' => $user]);
		}
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
			return $this->notFound();
		} elseif ($this->users->updateAsAdmin($user, Input::all())) {
			return Response::json(['user' => $user]);
		} else {
			return $this->error($this->users->getErrors());
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
			return $this->notFound();
		} elseif ($this->users->delete($user)) {
			return $this->success();
		} else {
			return $this->error('delete failed');
		}
	}

	/**
	 * Store a new user in the database.
	 *
	 * @return Redirect
	 */
	public function store()
	{
		if ($user = $this->users->create(Input::all())) {
			return Response::json(['user' => $user]);
		} else {
			return $this->error($this->users->getErrors());
		}
	}
}
