<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Web\Composers;

use Illuminate\Session\Store;
use Illuminate\Translation\Translator;
use Illuminate\View\View;
use Illuminate\Support\ViewErrorBag;

/**
 * View composer for resources/views/alerts.php
 */
class AlertsViewCreator
{
	protected $translator;
	protected $session;

	public function __construct(Store $session, Translator $translator)
	{
		$this->session = $session;
		$this->translator = $translator;
	}

	public function create(View $view)
	{
		$view->close = $this->translator->get('c::std.close');
		$view->validationErrors = $this->getValidationErrors();
		$view->alerts = $this->getAlerts();
	}

	protected function getAlerts()
	{
		$alerts = [];

		foreach (['success', 'warning', 'info', 'error'] as $key) {
			if ($this->session->has($key)) {
				$alerts[] = $this->makeAlert($key, $this->session->get($key));
			}
		}

		return $alerts;
	}

	protected function getValidationErrors()
	{
		if ($this->session->has('errors')) {
			return $this->session->get('errors')->all();
		}
	}

	protected function makeAlert($type, $message)
	{
		return (object) [
			'type'    => $type,
			'message' => ucfirst($message),
		];
	}
}
