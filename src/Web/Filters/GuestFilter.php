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
use Illuminate\Support\Facades\Response;
use Illuminate\Config\Repository;

class GuestFilter
{
	/**
	 * @var AuthManager|\Illuminate\Auth\Guard
	 */
	protected $auth;

	/**
	 * @var Repository
	 */
	protected $config;

	/**
	 * @var Redirector
	 */
	protected $redirect;

	/**
	 * @var UrlGenerator
	 */
	protected $url;

	public function __construct(
		AuthManager $auth,
		Repository $config,
		Redirector $redirect,
		UrlGenerator $url
	) {
		$this->auth = $auth;
		$this->config = $config;
		$this->redirect = $redirect;
		$this->url = $url;
	}

	public function filter(Route $route, Request $request)
	{
		if ($this->auth->check()) {
			$url = $this->url->to($this->config->get('c::redirect-login'), '/');

			return $this->redirect->to($url);
		}
	}
}
