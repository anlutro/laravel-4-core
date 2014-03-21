<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Activation;

/**
 * Interface that a code repository must implement.
 */
interface ActivationCodeRepositoryInterface
{
	/**
	 * Create a new activation code for a user.
	 *
	 * @param  ActivatableInterface $user
	 * @param  string               $code
	 *
	 * @return boolean
	 */
	public function create(ActivatableInterface $user, $code);

	/**
	 * Retrieve the email of a user based on the activation code from the
	 * database. Returns null if the code either does not exist or is expired.
	 *
	 * @param  string $code
	 *
	 * @return null|string
	 */
	public function retrieveEmailByCode($code);

	/**
	 * Delete a code from the database.
	 *
	 * @param  string $code
	 *
	 * @return boolean
	 */
	public function delete($code);

	/**
	 * Delete all codes related to a user from the database.
	 *
	 * @param  ActivatableInterface $user
	 *
	 * @return boolean
	 */
	public function deleteUser(ActivatableInterface $user);
}
