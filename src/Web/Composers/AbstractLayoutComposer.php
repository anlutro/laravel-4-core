<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Web\Composers;

use Illuminate\View\View;

/**
 * Abstract class that provides convenience methods for adding scripts, styles
 * and conditionals to the main layout.
 */
abstract class AbstractLayoutComposer
{
	public abstract function compose(View $view);

	protected function addHeadScript(View $view, $url, $priority = 0)
	{
		$view->headScripts->add($url, $priority);
	}

	protected function addBodyScript(View $view, $url, $priority = 0)
	{
		$view->bodyScripts->add($url, $priority);
	}

	protected function addStyle(View $view, $url, $priority = 0)
	{
		$view->styles->add($url, $priority);
	}

	protected function addConditional(View $view, $condition, $script)
	{
		$view->conditionals[$condition][] = $script;
	}
}
