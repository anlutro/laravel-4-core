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
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ErrorHandler
{
	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * Register the error handler.
	 *
	 * This method will call pushError() on the application's exception handler
	 * to put the handlers at the bottom of the stack, giving them the lowest
	 * possible priority. This means that other error handlers that log the
	 * errors, send notifications etc. can do their job but return null, then
	 * these handlers take over.
	 *
	 * When using pushError, the most specific handlers should be defined first.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->pushError(function(NotFoundHttpException $e) {
			return $this->handleMissing($e);
		});

		$this->app->pushError(function(TokenMismatchException $e) {
			return $this->handleTokenMismatch($e);
		});

		$this->app->pushError(function(Exception $e) {
			return $this->handleGeneric($e);
		});

		$this->app->down(function() {
			return $this->handleMaintenance();
		});
	}

	protected function handleMissing(NotFoundHttpException $e)
	{
		if ($this->app->runningInConsole()) {
			return null;
		}

		if ($this->shouldRespondWithJson()) {
			return new JsonResponse(['status' => 'missing'], 404);
		}

		$translator = $this->app['translator'];
		$url = $this->app['url'];
		$view = $this->app['view'];

		$contents = $view->make('c::error', [
			'title' => $translator->get('smarterror::error.missingTitle'),
			'text' => [$translator->get('smarterror::error.missingText')],
			'homeUrl' => $url->to('/'),
		]);

		return new Response($contents, 404);
	}

	protected function handleTokenMismatch()
	{
		if ($this->shouldRespondWithJson()) {
			return new JsonResponse(['status' => 'token_mismatch'], 400);
		}

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

		if ($this->shouldRespondWithJson()) {
			return new JsonResponse(['status' => 'internal error'], 500);
		}

		$viewFactory = $this->app['view'];
		$translator = $this->app['translator'];
		$urlGenerator = $this->app['url'];

		$view = $viewFactory->make('c::error', [
			'title' => $translator->get('smarterror::error.genericErrorTitle'),
			'text' => [
				$translator->get('smarterror::error.genericErrorParagraph1'),
				$translator->get('smarterror::error.genericErrorParagraph2'),
			],
			'homeUrl' => $urlGenerator->to('/'),
		]);

		if ($this->app['config']->get('c::support-email')) {
			$url = $urlGenerator->route('c::support');
			$view->text[] = $translator->get('c::support.send-text', [
				'link' => '<a href="'.$url.'">'.$url.'</a>',
			]);
		}

		return new Response($view->render(), 500);
	}

	protected function handleMaintenance()
	{
		$translator = $this->app['translator'];

		$contents = $translator->get('c::std.maintenance-info');

		return new Response($contents, 503);
	}

	protected function shouldRespondWithJson()
	{
		$request = $this->app['request'];

		return $request->ajax() || $request->isJson() || $request->wantsJson();
	}
}
