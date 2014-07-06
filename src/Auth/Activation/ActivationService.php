<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Activation;

use Illuminate\Auth\UserProviderInterface;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Translation\Translator;

class ActivationService
{
	protected $codes;
	protected $users;
	protected $mailer;
	protected $translator;
	protected $hashKey;
	protected $queue;

	public function __construct(
		ActivationCodeRepositoryInterface $codes,
		UserProviderInterface $users,
		Mailer $mailer,
		Translator $translator,
		$hashKey,
		$queue = false
	) {
		$this->codes = $codes;
		$this->users = $users;
		$this->mailer = $mailer;
		$this->translator = $translator;
		$this->hashKey = $hashKey;
		$this->queue = $queue;
	}

	/**
	 * Generate a new activation code for a user and send it via e-mail.
	 *
	 * @param  ActivatableInterface $user
	 *
	 * @return void
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
		$this->emailActivationCode($user, $code);
	}

	/**
	 * Send an email to a user with an activation code.
	 *
	 * @param  ActivatableInterface $user
	 * @param  string               $code
	 *
	 * @return void
	 *
	 * @throws ActivationException
	 */
	protected function emailActivationCode(ActivatableInterface $user, $code)
	{
		$email = $user->getActivationEmail();

		$method = $this->queue ? 'queue' : 'send';

		$viewData = [
			'code' => $code,
			'action' => 'anlutro\Core\Web\AuthController@activate',
		];

		$this->mailer->$method('c::auth.activate-email', $viewData, function(Message $msg) use ($email) {
			$msg->to($email)
				->subject($this->translator->get('c::auth.activate-title'));
		});

		if (count($this->mailer->failures()) > 0) {
			throw new ActivationException('Could not send activation e-mail');
		}
	}

	/**
	 * Activate a user via an activation code.
	 *
	 * @param  string $code
	 *
	 * @return void
	 *
	 * @throws \anlutro\Core\Auth\Activation\ActivationException
	 */
	public function activate($code)
	{
		$email = $this->codes->retrieveEmailByCode($code);

		if (!$email) {
			throw new ActivationException('No email found for activation code');
		}

		$user = $this->findUserByEmail($email);

		if (!$user) {
			throw new ActivationException('Email found for activation code, but no user found for email');
		}

		$this->activateUser($user);

		$this->codes->delete($code);
	}

	/**
	 * Find the user an activation code belongs to.
	 *
	 * @param  string $code
	 *
	 * @return mixed
	 */
	protected function findUserByEmail($email)
	{
		$credentials = ['email' => $email];

		return $this->users->retrieveByCredentials($credentials);
	}

	/**
	 * Activate a user.
	 *
	 * @param  ActivatableInterface $user
	 *
	 * @return void
	 */
	protected function activateUser(ActivatableInterface $user)
	{
		try {
			$user->activate();
		} catch (\Exception $e) {
			throw new ActivationException('Could not activate user - '.$e->getMessage(), $e->getCode(), $e);
		}
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
