<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Reminders;

use Illuminate\Mail\Message;
use Illuminate\Mail\Mailer;
use Illuminate\Auth\UserProviderInterface;
use Illuminate\Translation\Translator;

/**
 * Class responsible for handling password resets. Improved version of
 * Illuminate\Auth\Reminders\PasswordBroker - doesn't return redirect responses
 * with session data hard-coded in, but rather does things behind the scenes,
 * returns boolean flags or throws exceptions and lets the developer decide what
 * should be done with the results.
 */
class PasswordBroker
{
	protected $users;
	protected $reminders;
	protected $mailer;
	protected $translator;
	protected $emailView;
	protected $queue = false;

	public function __construct(
		UserProviderInterface $users,
		DatabaseReminderRepository $reminders,
		Mailer $mailer,
		Translator $translator,
		array $config
	) {
		$this->users = $users;
		$this->reminders = $reminders;
		$this->mailer = $mailer;
		$this->translator = $translator;
		$this->emailView = $config['email-view'];
		$this->queue = $config['queue-email'];
	}

	/**
	 * Register a request for a password reset. Create a reset token for a user
	 * and send him/her an email with instructions on how to reset password.
	 *
	 * @param  RemindableInterface $user
	 *
	 * @return boolean
	 */
	public function requestReset(RemindableInterface $user)
	{
		// delete existing tokens belongning to user
		$this->reminders->deleteUser($user);

		// create a new token for the user
		$token = $this->reminders->create($user);

		// send an email
		$this->mail($user, $token);

		return true;
	}

	/**
	 * Send an email to a user with instructions on how to reset his/her password.
	 *
	 * @param  RemindableInterface $user
	 * @param  string $token
	 *
	 * @return boolean
	 */
	public function mail(RemindableInterface $user, $token)
	{
		$email = $user->getReminderEmail();

		$method = $this->queue ? 'queue' : 'send';

		$viewData = [
			'token' => $token,
			'action' => 'anlutro\Core\Web\AuthController@reset',
		];

		$this->mailer->$method($this->emailView, $viewData, function(Message $msg) use ($email) {
			$msg->to($email)
				->subject($this->translator->get('c::auth.resetpass-title'));
		});

		return true;
	}

	/**
	 * Reset a user's password.
	 *
	 * @param  RemindableInterface $user
	 * @param  string              $token
	 * @param  string              $newPassword
	 *
	 * @return boolean
	 */
	public function resetUser(RemindableInterface $user, $token, $newPassword)
	{
		if (!$this->reminders->exists($user, $token)) {
			return false;
		}

		$user->setPassword($newPassword);
		$this->reminders->delete($token);

		return true;
	}
}
