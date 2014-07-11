<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Web;

use anlutro\LaravelController\ApiController;
use anlutro\LaravelValidation\ValidationException;

use anlutro\Core\Auth\UserManager;

/**
 * Controller for managing users, not including authentication.
 */
class ApiUserController extends ApiController
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
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function profile()
	{
		$user = $this->users->getCurrentUser();

		return $this->jsonResponse(['user' => $user]);
	}

	/**
	 * Update the logged in user's profile.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function updateProfile()
	{
		try {
			$this->users->updateCurrentProfile($this->input());
			return $this->jsonResponse(['user' => $this->users->getCurrentUser()]);
		} catch (ValidationException $e) {
			return $this->error($e->getMessageBag());
		}
	}

	/**
	 * Show a table of users.
	 *
	 * @return \Illuminate\Http\JsonResponse
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

		return $this->jsonResponse(['users' => $users]);
	}

	/**
	 * Apply an action on more than one user.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function bulk()
	{
		$userIds = $this->input('bulk');
		$action = $this->input('bulkAction');

		$result = $this->users->processBulkAction($action, $userIds);

		return $this->success("$result affected rows");
	}

	/**
	 * Show a user's info.
	 *
	 * @param  int $userId
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function show($userId)
	{
		if (!$user = $this->users->findByKey($userId)) {
			return $this->notFound();
		} else{
			return $this->jsonResponse(['user' => $user]);
		}
	}

	/**
	 * Update a user's information.
	 *
	 * @param  int $userId
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update($userId)
	{
		if (!$user = $this->users->findByKey($userId)) {
			return $this->notFound();
		}

		try {
			$this->users->updateAsAdmin($user, $this->input());
			return $this->jsonResponse(['user' => $user]);
		} catch (ValidationException $e) {
			return $this->error($e->getMessageBag());
		}
	}

	/**
	 * Delete an existing user.
	 *
	 * @param  int $userId
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete($userId)
	{
		if (!$user = $this->users->findByKey($userId)) {
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
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function store()
	{
		try {
			$user = $this->users->create($this->input());
			return $this->jsonResponse(['user' => $user]);
		} catch (ValidationException $e) {
			return $this->error($e->getMessageBag());
		}
	}
}
