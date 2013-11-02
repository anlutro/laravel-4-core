<?php
/**
 * Laravel 4 Core - TestCase
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c;

/**
 * Abstract TestCase with a lot of handy functions.
 */
abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
	/**
	 * The controller to test, fully namespaced.
	 *
	 * @var string
	 */
	protected $controller;

	/**
	 * Call a controller action and return the Response.
	 *
	 * @param  string  $method
	 * @param  string  $action
	 * @param  array   $parameters
	 * @param  array   $input
	 * @param  array   $files
	 * @param  array   $server
	 * @param  string  $content
	 * @param  bool    $changeHistory
	 * @return \Illuminate\Http\Response
	 */
	public function action($method, $action, $parameters = array(), $input = array(), $files = array(), $server = array(), $content = null, $changeHistory = true)
	{
		return parent::action($method, $this->parseAction($action), $parameters, $files, $server, $content, $changeHistory);
	}

	/**
	 * Perform a GET request on a controller action.
	 *
	 * @param  string $action name of the action.
	 * @param  array  $params (optional) route parameters
	 * @param  array  $input  (optional) GET parameters
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getAction($action, $params = array(), $input = array())
	{
		return $this->action('GET', $action, $params, $input);
	}

	/**
	 * Perform a POST request on a controller action.
	 *
	 * @param  string $action name of the action.
	 * @param  array  $params (optional) route parameters
	 * @param  array  $input  (optional) input data
	 * @param  array  $files  (optional) files data
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function postAction($action, $params = array(), $input = array(), $files = array())
	{
		return $this->action('POST', $action, $params, $input);
	}

	/**
	 * Assert that we're redirected to an action. If $this->controller is
	 * set, you just need the action name.
	 *
	 * @param  string $action name of the action
	 * @param  array  $params (optional) route parameters
	 * @param  array  $with   (optional) session data
	 *
	 * @return void
	 */
	public function assertRedirectedToAction($action, $params = array(), $with = array())
	{
		parent::assertRedirectedToAction($this->parseAction($action), $params, $with);
	}

	/**
	 * Helper function to assert that the current route has a filter.
	 *
	 * @param  string $filtername name of the filter.
	 * @param  string $when       before|after
	 *
	 * @return void
	 */
	public function assertRouteHasFilter($filtername, $when = 'before')
	{
		$route = $this->app['router']->current();

		if ($when == 'before') {
			$filters = $route->beforeFilters();
		} elseif ($when == 'after') {
			$filters = $route->afterFilters();
		} else {
			throw new \InvalidArgumentException('$when must be "before" or "after"');
		}

		if ($route->getName()) {
			$routeName = $route->getName();
		} else {
			$routeName = $route->getActionName();
		}

		if (strpos($filtername, ':') === false) {
			$name = $filtername;
			$params = array();
		} else {
			list($name, $params) = explode(':', $filtername);
			if (strpos(',', $params) !== false) {
				$params = explode(',', $params);
			} else {
				$params = array($params);
			}
		}

		$filter = [$name => $params];
		$this->assertTrue((isset($filters[$name]) && $filters[$name] === $params),
			"Filter $filtername not present in $routeName");
	}

	/**
	 * Check that an input field has a certain value.
	 * 
	 * WARNING: Does not work with Form::model for some reason unless you
	 * manually specify the values in the Form::input calls
	 *
	 * @param  string $id    id of the input field
	 * @param  string $value expected value
	 *
	 * @return void
	 */
	public function assertInputHasValue($id, $value)
	{
		$realValue = $this->crawler->filter('input#'.$id)->first()->attr('value');

		$this->assertEquals($realValue, $value,
			"Unexpected value in input#{$id}: $realValue -- expected $value");
	}

	/**
	 * Parse an action input and try to guess the controller/namespace based
	 * on whether or not the input has a @ or \. If one or more aren't present,
	 * guess based on $this->controller.
	 *
	 * @param  string $action
	 *
	 * @return string fully namespaced Controller@Action
	 */
	public function parseAction($action)
	{
		if (!isset($this->controller)) {
			return $action;
		}

		if (strpos($action, '@') === false) {
			return $this->controller . '@' . $action;
		} elseif (strpos($action, '\\') === false) {
			$namespace = substr($this->controller, 0, strrpos($this->controller, '\\'));
			return $namespace . '\\' . $action;
		} else {
			return $action;
		}
	}
}
