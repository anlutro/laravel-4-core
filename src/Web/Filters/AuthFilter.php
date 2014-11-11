<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Web\Filters;

use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Route;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Response;
use Illuminate\Translation\Translator;

class AuthFilter
{
	/**
	 * @var AuthManager|\Illuminate\Auth\Guard
	 */
	protected $auth;

	/**
	 * @var Redirector
	 */
	protected $redirect;

	/**
	 * @var Store
	 */
	protected $session;

	/**
	 * @var Translator
	 */
	protected $translator;

	/**
	 * @var UrlGenerator
	 */
	protected $url;

	public function __construct(
		AuthManager $auth,
		Redirector $redirect,
		Translator $translator,
		Store $session,
		UrlGenerator $url
	) {
		$this->auth = $auth;
		$this->redirect = $redirect;
		$this->session = $session;
		$this->translator = $translator;
		$this->url = $url;
	}

	public function filter(Route $route, Request $request)
	{
		if ($this->auth->guest()) {
			return $this->makeResponse($request);
		}
	}

	protected function makeResponse(Request $request)
	{
		$message = $this->translator->get('c::auth.login-required');

		if ($request->ajax() || $request->isJson() || $request->wantsJson()) {
			return Response::json(['error' => $message], 403);
		} else {
			$url = $this->url->action('anlutro\Core\Web\AuthController@login');

			$intended = $request->getMethod() == 'GET' ? $request->fullUrl() :
				($request->header('referer') ?: '/');
			$this->session->put('url.intended', $intended);

			return $this->redirect->to($url)
				->with('error', $message);
		}
	}
}
