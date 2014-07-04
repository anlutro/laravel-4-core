<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;

class ErrorHandler
{
	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	public function register()
	{
		// 404/missing handler followed by the generic uncaught exception handler. use
		// pushError to push them at the end of the stack, giving them lower priority.
		// because we use pushError, the most specific handler needs to be defined first
		$this->app->pushError(function(NotFoundHttpException $e) {
			return $this->handleMissing($e);
		});

		$this->app->pushError(function(TokenMismatchException $e) {
			return $this->handleTokenMismatch($e);
		});

		$this->app->pushError(function(Exception $e) {
			return $this->handleGeneric($e);
		});

		// maintenance mode handler
		$this->app->down(function() {
			return $this->handleMaintenance();
		});
	}

	protected function handleMissing(NotFoundHttpException $e)
	{
		if ($this->app->runningInConsole()) {
			return null;
		}

		$view = $this->app['view'];
		$translator = $this->app['translator'];
		$url = $this->app['url'];

		$contents = $view->make('c::error', [
			'title' => $translator->get('smarterror::error.missingTitle'),
			'text' => [$translator->get('smarterror::error.missingText')],
			'homeUrl' => $url->to('/'),
		]);

		return new Response($contents, 404);
	}

	protected function handleTokenMismatch()
	{
		$view = $this->app['view'];
		$translator = $this->app['translator'];
		$url = $this->app['url'];

		$contents = $view->make('c::error', [
			'title' => $translator->get('smarterror::error.csrfTitle'),
			'text' => [$translator->get('smarterror::error.csrfText')],
			'homeUrl' => $url->to('/'),
		]);

		return new Response($contents, 400);
	}

	protected function handleGeneric(Exception $e)
	{
		if ($this->app->runningInConsole() || $this->app['config']->get('app.debug')) {
			return null;
		}

		$view = $this->app['view'];
		$translator = $this->app['translator'];
		$url = $this->app['url'];

		$contents = $view->make('c::error', [
			'title' => $translator->get('smarterror::error.genericErrorTitle'),
			'text' => [
				$translator->get('smarterror::error.genericErrorParagraph1'),
				$translator->get('smarterror::error.genericErrorParagraph2'),
			],
			'homeUrl' => $url->to('/'),
		]);

		return new Response($contents, 500);
	}

	protected function handleMaintenance()
	{
		$translator = $this->app['translator'];

		$contents = $translator->get('c::std.maintenance-mode');

		return new Response($contents, 503);
	}
}
