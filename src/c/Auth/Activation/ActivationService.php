<?php
/**
 * Laravel 4 Core - User activation service
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth\Activation;

use Illuminate\Auth\UserProviderInterface;
use Illuminate\Mail\Mailer;

class ActivationService
{
	protected $codes;
	protected $users;
	protected $mailer;
	protected $hashKey;
	protected $queue = false;

	public function __construct(
		ActivationCodeRepositoryInterface $codes,
		UserProviderInterface $users,
		Mailer $mailer,
		$hashKey,
		$queue
	) {
		$this->codes = $codes;
		$this->users = $users;
		$this->mailer = $mailer;
		$this->hashKey = $hashKey;
		$this->queue = $queue;
	}

	/**
	 * Generate a new activation code for a user and send it via e-mail.
	 *
	 * @param  ActivatableInterface $user
	 *
	 * @return boolean
	 */
	public function generate(ActivatableInterface $user)
	{
		// deactivate the user and delete any existing activation tokens
		$user->deactivate();
		$this->codes->deleteUser($user);

		// generate a new token
		$code = $this->generateActivationCode($user);
		$this->codes->create($user, $code);

		// send the email
		return $this->emailActivationCode($user, $code);
	}

	/**
	 * Send an email to a user with an activation code.
	 *
	 * @param  ActivatableInterface $user
	 * @param  string               $code
	 *
	 * @return boolean
	 */
	protected function emailActivationCode(ActivatableInterface $user, $code)
	{
		$email = $user->getActivationEmail();

		$method = $this->queue ? 'queue' : 'send';

		$data = ['code' => $code];

		return $this->mailer->$method('c::auth.activate-email', $data, function($msg) use ($email) {
			$msg->to($email)
				->subject(Lang::get('c::auth.activate-title'));
		});
	}

	/**
	 * Activate a user via an activation code.
	 *
	 * @param  string $code
	 *
	 * @return boolean
	 */
	public function activate($code)
	{
		$code = $this->codes->retrieveByCode($code);

		if (!$code) {
			return false;
		}

		$user = $this->findUserByCode($code);

		if (!$user) {
			return false;
		}

		return ($this->activateUser($user) && $this->codes->delete($code->code));
	}

	/**
	 * Find the user an activation code belongs to.
	 *
	 * @param  string $code
	 *
	 * @return mixed
	 */
	protected function findUserByCode($code)
	{
		$credentials = ['email' => $code->email];
		return $this->users->retrieveByCredentials($credentials);
	}

	/**
	 * Activate a user.
	 *
	 * @param  ActivatableInterface $user
	 *
	 * @return boolean
	 */
	protected function activateUser(ActivatableInterface $user)
	{
		return $user->activate();
	}

	/**
	 * Generate a random activation code.
	 *
	 * @param  Object $object
	 *
	 * @return string
	 */
	protected function generateActivationCode($object)
	{
		$value = str_shuffle(sha1(spl_object_hash($this).spl_object_hash($object).microtime(true)));
		return hash_hmac('sha1', $value, $this->hashKey);
	}
}
