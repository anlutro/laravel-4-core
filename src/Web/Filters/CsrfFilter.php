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

class CsrfFilter
{
	protected $session;

	public function __construct(Store $session)
	{
		$this->session = $session;
	}

	public function filter(Route $route, Request $request)
	{
		if ($this->session->token() != $request->input('_token')) {
			throw new TokenMismatchException;
		} else {
			$this->session->regenerateToken();
		}
	}
}
