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

use anlutro\Core\Html\ScriptCollection;
use anlutro\Core\Html\ScriptManager;

class GenericLayoutCreator
{
	protected $config;
	protected $translator;

	public function __construct(
		Repository $config,
		Translator $translator,
		ScriptManager $scripts
	) {
		$this->config = $config;
		$this->translator = $translator;
		$this->scripts = $scripts;
	}

	public function create(View $view)
	{
		ScriptCollection::setGlobalDebug($this->config->get('app.debug'));

		$view->lang = $this->translator->getLocale();

		$view->styles = $this->scripts->get('style');
		$view->headScripts = $this->scripts->get('head');
		$view->bodyScripts = $this->scripts->get('body');

		$view->conditionals = [];

		$view->title = $this->getTitle();
		$view->description = $this->getDescription();
		$view->gaCode = $this->getGaCode();
	}

	protected function getTitle()
	{
		return $this->config->get('c::site.name') ?: $this->config->get('app.url');
	}

	protected function getDescription()
	{
		return $this->translator->get('c::site.description');
	}

	protected function getGaCode()
	{
		return $this->config->get('c::site.ga-code');
	}
}
