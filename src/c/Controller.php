<?php
/**
 * Laravel 4 Core - Base Controller class
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

/**
 * Abstract controller with a lot of handy functions.
 */
abstract class Controller extends \Illuminate\Routing\Controller
{
	/**
	 * The fully namespaced class name. Used by helper methods. If you set it
	 * yourself you'll save PHP a tiny bit of processing power, otherwise it'll
	 * still find out itself.
	 *
	 * @var string
	 */
	protected $classname;

	/**
	 * Helper function to retrieve this controller's action URLs.
	 * 
	 * @see    parseAction
	 *
	 * @param  string $action name of the action to look for
	 * @param  array  $params route parametersa
	 *
	 * @return string         the URL to the action.
	 */
	protected function urlAction($action, $params = array())
	{
		$action = $this->parseAction($action);

		return URL::action($action, $params);
	}

	/**
	 * Helper function to redirect to another action in the controller.
	 * 
	 * @see    parseAction
	 *
	 * @param  string $action name of the action to look for
	 * @param  array  $params (optional) additional parameters
	 *
	 * @return Redirect       a Redirect response.
	 */
	protected function redirectAction($action, $params = array())
	{
		$action = $this->parseAction($action);

		return Redirect::action($action, $params);
	}

	/**
	 * Parse an action input and try to guess the classname/namespace based on
	 * whether or not the input has a @ or \. If one or more aren't present,
	 * guess based on $this->classname.
	 *
	 * @param  string $action
	 *
	 * @return string fully namespaced Controller@Action
	 */
	protected function parseAction($action)
	{
		if (!isset($this->classname)) {
			$this->classname = get_class($this);
		}

		if (strpos($action, '@') === false) {
			return $this->classname . '@' . $action;
		} elseif (strpos($action, '\\') === false) {
			$namespace = substr($this->classname, 0, strrpos($this->classname, '\\'));
			if (!empty($namespace)) {
				return $namespace . '\\' . $action;
			} else {
				return $action;
			}
		} else {
			return $action;
		}
	}
}
