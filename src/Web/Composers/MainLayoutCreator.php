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

use anlutro\Core\View\ScriptCollection;

class MainLayoutCreator
{
	protected $config;
	protected $translator;

	public function __construct(Repository $config, Translator $translator)
	{
		$this->config = $config;
		$this->translator = $translator;
	}

	public function create(View $view)
	{
		$view->styles = new ScriptCollection;
		$view->headScripts = new ScriptCollection;
		$view->bodyScripts = new ScriptCollection;
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
		return $this->translator->get('c::site.ga-code');
	}
}
