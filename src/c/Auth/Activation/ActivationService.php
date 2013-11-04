<?php
namespace c\Auth\Activation;

use Illuminate\Auth\UserProviderInterface;
use Illuminate\Mail\Mailer;

class ActivationService
{
	public function __construct(
		ActivationCodeRepositoryInterface $codes,
		UserProviderInterface $users,
		Mailer $mailer,
		$hashKey
	) {
		$this->codes = $codes;
		$this->users = $users;
		$this->mailer = $mailer;
		$this->hashKey = $hashKey;
	}

	public function generate(ActivatableInterface $user)
	{
		$code = $this->generateActivationCode($user);
		$this->codes->create($user, $code);
		return $this->emailActivationCode($user, $code);
	}

	protected function emailActivationCode(ActivatableInterface $user, $code)
	{
		$email = $user->getActivationEmail();

		$data = ['code' => $code];

		return $this->mailer->send('c::auth.activate-email', $data, function($msg) use ($email) {
			$msg->to($email)
				->subject(Lang::get('c::auth.activate-title'));
		});
	}

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

	protected function findUserByCode($code)
	{
		$credentials = ['email' => $code->email];
		return $this->users->retrieveByCredentials($credentials);
	}

	protected function activateUser(ActivatableInterface $user)
	{
		return $user->activate();
	}

	protected function generateActivationCode($object)
	{
		$value = str_shuffle(sha1(spl_object_hash($this).spl_object_hash($object).microtime(true)));
		return hash_hmac('sha1', $value, $this->hashKey);
	}
}
