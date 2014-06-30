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
use Illuminate\Config\Repository as Config;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Response;
use Illuminate\Translation\Translator;

class AccessFilter
{
	public function __construct(
		AuthManager $auth,
		Config $config,
		Translator $translator,
		UrlGenerator $url
	) {
		$this->auth = $auth;
		$this->config = $config;
		$this->translator = $translator;
		$this->url = $url;
	}

	public function filter(Route $route, Request $request, $params)
	{
		if (!$user = $this->auth->user()) {
			throw new \RuntimeException('auth filter must precede access filter');
		}

		foreach ((array) $params as $access) {
			if (!$user->hasAccess($access)) {
				return $this->makeResponse($request);
			}
		}
	}

	protected function makeResponse(Request $request)
	{
		if ($request->ajax() || $request->isJson() || $request->wantsJson()) {
			return Response::json(['error' => $this->getErrorMessage()], 403);
		} else {
			$data = [
				'title' => $this->getErrorTitle(),
				'text' => [$this->getErrorMessage()],
				'homeUrl' => $this->getHomeUrl(),
			];
			return Response::view('c::error', $data, 403);
		}
	}

	protected function getErrorTitle()
	{
		return $this->translator->get('c::std.error');
	}

	protected function getErrorMessage()
	{
		return $this->translator->get('c::auth.access-denied');
	}

	protected function getHomeUrl()
	{
		$path = $this->config->get('c::redirect-login', '/');
		return $this->url->to($path);
	}
}