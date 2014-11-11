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
	/**
	 * The view instance.
	 *
	 * @var View
	 */
	private $view;

	/**
	 * Compose the view.
	 *
	 * @param  View   $view
	 *
	 * @return void
	 */
	public final function compose(View $view)
	{
		$this->view = $view;
		$this->addScripts();
	}

	/**
	 * Inheriting classes should call this method and call various protected
	 * methods to add scripts and styles.
	 *
	 * @return void
	 */
	protected abstract function addScripts();

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
