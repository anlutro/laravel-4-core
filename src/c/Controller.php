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
	 * If any \ are present, just return the string as is. If no \ are, but @ is
	 * present, takes the current namespace and adds the given controller name.
	 * If \ nor @ are present, takes the current controller class name and
	 * appends the given action.
	 *
	 * @param  string $action
	 *
	 * @return string fully namespaced Controller@Action
	 */
	protected function parseAction($action)
	{
		static $classname;

		if ($classname === null) {
			$classname = get_class($this);
		}

		if (strpos($action, '@') === false) {
			return $classname . '@' . $action;
		} elseif (strpos($action, '\\') === false) {
			$namespace = substr($classname, 0, strrpos($classname, '\\'));
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
