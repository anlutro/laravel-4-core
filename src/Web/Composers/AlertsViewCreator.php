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
		$view->alerts = $this->getAlerts();
	}

	protected function getAlerts()
	{
		$alerts = [];

		if ($this->session->has('errors')) {
			foreach ($this->session->get('errors')->all() as $error) {
				$alerts[] = $this->makeAlert('danger', $error);
			}
		}

		foreach (['warning', 'info', 'success'] as $key) {
			if ($this->session->has($key)) {
				$alerts[] = $this->makeAlert($key, $this->session->get($key));
			}
		}

		return $alerts;
	}

	protected function makeAlert($type, $message)
	{
		return (object) [
			'type'    => $type,
			'message' => ucfirst($message),
		];
	}
}
