<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Laravel;

use Illuminate\Auth\UserInterface;

use anlutro\Core\Auth\AuthenticationException;

/**
 * Override the default Eloquent userprovider to throw exceptions which are
 * more helpful during development/debugging.
 */
class EloquentUserProvider extends \Illuminate\Auth\EloquentUserProvider
{
	/**
	 * {@inheritdoc}
	 */
	public function retrieveByCredentials(array $credentials)
	{
		if ($user = parent::retrieveByCredentials($credentials)) {
			return $user;
		}

		$credStr = $this->getCredentialsString($credentials);

		throw new AuthenticationException("No user with these credentials found - $credStr");
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getCredentialsString(array $credentials)
	{
		$credStr = '';

		foreach ($credentials as $key => $value) {
			if (strpos($key, 'password') !== false) continue;
			$credStr .= "`$key` => \"$value\" - ";
		}

		return rtrim($credStr, ' -');
	}

	/**
	 * {@inheritdoc}
	 */
	public function validateCredentials(UserInterface $user, array $credentials)
	{
		if (parent::validateCredentials($user, $credentials)) {
			return true;
		}

		throw new AuthenticationException("Incorrect password");
	}

	/**
	 * {@inheritdoc}
	 */
	public function updateRememberToken(UserInterface $user, $token)
	{
		$user->setRememberToken($token);
		// the original class has a save() call here
	}
}
