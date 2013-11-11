<?php
/**
 * Laravel 4 Core - Activation code repository interface
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth\Activation;

interface ActivationCodeRepositoryInterface
{
	public function create(ActivatableInterface $user, $code);
	public function retrieveByCode($code);
	public function delete($code);
}
