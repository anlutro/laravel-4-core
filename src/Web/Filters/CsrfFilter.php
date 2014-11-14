<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Web\Filters;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Session\Store;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\Security\Core\Util\StringUtils;

class CsrfFilter
{
	protected $session;
	protected $regenerate;

	public function __construct(Store $session, $regenerate = true)
	{
		$this->session = $session;
		$this->regenerate = $regenerate;
	}

	public function filter(Route $route, Request $request)
	{
		if (!StringUtils::equals($this->session->token(), $request->input('_token'))) {
			throw new TokenMismatchException;
		}

		if ($this->regenerate) {
			$this->session->regenerateToken();
		}
	}
}
