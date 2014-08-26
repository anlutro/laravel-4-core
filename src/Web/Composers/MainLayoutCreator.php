<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Web\Composers;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Translation\Translator;
use Illuminate\View\View;

class MainLayoutCreator
{
	protected $app;
	protected $config;
	protected $translator;

	public function __construct(
		Application $app,
		Repository $config,
		Translator $translator
	) {
		$this->app = $app;
		$this->config = $config;
		$this->translator = $translator;
	}

	public function create(View $view)
	{
		$view->footer = [];

		$view->footer[] = $this->config->get('c::site.copyright-date').' &copy; '.$this->config->get('c::site.copyright-holder');

		if ($this->translator->has('c::site.made-by')) {
			$view->footer[] = $this->translator->get('c::site.made-by');
		}

		$env = $this->app->environment();

		if ($env !== 'production') {
			$view->footer[] = 'Application environment: ' . $env;
		}
	}
}
