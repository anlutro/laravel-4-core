<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Web;

use anlutro\LaravelController\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
	public function displayForm()
	{
		return $this->view('c::support.form', [
			'formAction' => $this->url('@handleForm'),
		]);
	}

	public function handleForm()
	{
		$input = $this->input(['subject', 'email', 'phone', 'body']);

		if ($errors = $this->getValidationErrors($input)) {
			return $this->redirect('@displayForm')
				->withInput()->withErrors($errors);
		}

		if (!$this->sendMail($input)) {
			return $this->redirect('@displayForm')->withInput()
				->with('error', Lang::get('c::support.mail-failure'));
		}

		return Redirect::to(Config::get('c::redirect-login'))
			->with('success', Lang::get('c::support.mail-success'));
	}

	protected function getValidationErrors(array $input)
	{
		$rules = [
			'subject' => ['required'],
			'email'   => ['email'],
			'phone'   => [],
			'body'    => ['required'],
		];

		$validator = Validator::make($input, $rules);

		return $validator->fails() ? $validator : null;
	}

	protected function sendMail($input)
	{
		$data = [
			'user' => Auth::user(),
			'title' => Lang::get('c::support.subject'),
		] + $input;

		if (!Config::get('c::support-email')) {
			throw new \RuntimeException('Cannot send support e-mail - c::support-email config value is empty');
		}

		Mail::send('c::support.mail', $data, function($msg) {
			$msg->to(Config::get('c::support-email'))
				->subject(Lang::get('c::support.subject'));
		});

		return count(Mail::failures()) == 0;
	}
}
