<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core;

use Illuminate\Session\Store as Session;

/**
 * View composer for resources/views/alerts.php
 */
class AlertsComposer
{
	protected $session;

	public function __construct(Session $session)
	{
		$this->session = $session;
	}

	public function compose($view)
	{
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
		return (object) ['type' => $type, 'message' => $message];
	}
}
