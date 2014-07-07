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

class EloquentUserProvider extends \Illuminate\Auth\EloquentUserProvider
{
	public function retrieveById($id)
	{
		if ($user = parent::retrieveById($id)) {
			return $user;
		}
		
		throw new AuthenticationException("No user with identifier [$id] found");
	}

	public function retrieveByToken($id, $token)
	{
		if ($user = parent::retrieveByToken($id, $token)) {
			return $user;
		}
		
		throw new AuthenticationException("No user with identifier [$id] and token [$token] found");
	}

	public function retrieveByCredentials(array $credentials)
	{
		if ($user = parent::retrieveByCredentials($credentials)) {
			return $user;
		}

		$credStr = $this->getCredentialsString($credentials);

		throw new AuthenticationException("No user with these credentials found - $credStr");
	}

	protected function getCredentialsString(array $credentials)
	{
		$credStr = '';

		foreach ($credentials as $key => $value) {
			if (strpos($key, 'password') !== false) continue;
			$credStr .= "`$key` => \"$value\" - ";
		}

		return rtrim($credStr, ' -');
	}

	public function validateCredentials(UserInterface $user, array $credentials)
	{
		if (parent::validateCredentials($user, $credentials)) {
			return true;
		}

		throw new AuthenticationException("Incorrect password");
	}
}