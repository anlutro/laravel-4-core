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
use Illuminate\Translation\Translator;
use Illuminate\View\View;

class MainLayoutCreator
{
	public function __construct(
		Repository $config,
		Translator $translator
	) {
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
	}
}
