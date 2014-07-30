<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Html;

use ArrayIterator;
use IteratorAggregate;

/**
 * Collection for holding HTML scripts such as CSS and JS files.
 */
class ScriptCollection implements IteratorAggregate
{
	protected static $globalDebug = false;
	protected $cacheBuster;
	protected $debug = false;
	protected $scripts = [];

	public static function setGlobalDebug($toggle)
	{
		static::$globalDebug = (bool) $toggle;
	}

	public function __construct($debug = null, $cacheBuster = null)
	{
		$this->setDebug($debug);
		$this->setCacheBuster($cacheBuster);
	}

	public function setDebug($toggle)
	{
		if ($toggle === null) {
			$this->debug = static::$globalDebug;
		} else {
			$this->debug = (bool) $toggle;
		}
	}

	public function setCacheBuster($cacheBuster)
	{
		if ($cacheBuster !== null && !is_string($cacheBuster)) {
			throw new \InvalidArgumentException('Cache buster must be a string, '
				.gettype($cacheBuster).' given');
		}

		$this->cacheBuster = $cacheBuster;
	}

	/**
	 * Add a script to the collection.
	 *
	 * @param array|string  $script   Relative URI to the script or array of [devUrl, prodUrl, version] where version is optional
	 * @param integer       $priority Larger number = higher priority = comes before other scripts
	 */
	public function add($script, $priority = 0)
	{
		$script = $this->getScriptArray($script, 'Argument 1 passed to '.__METHOD__);

		if (!is_integer($priority)) {
			$message = 'Argument 2 passed to '.__METHOD__.' must be of the type integer, '.gettype($priority).' given';
			throw new \InvalidArgumentException($message);
		}

		$this->scripts[$priority][] = $script;
	}

	protected function getScriptArray($script, $message)
	{
		if (is_string($script)) {
			$script = [$script, $script];
		} else if (!is_array($script)) {
			$message .= ' must be of the type string or array, '.gettype($script).' given';
			throw new \InvalidArgumentException($message);
		}

		return $script;
	}

	/**
	 * Remove a script from the collection.
	 *
	 * @param  string $url
	 *
	 * @return void
	 */
	public function remove($url)
	{
		foreach ($this->scripts as $key => $scripts) {
			foreach ($scripts as $innerKey => $script) {
				if ($url == $script[0] || $url == $script[1]) {
					unset($this->scripts[$key][$innerKey]);
				}
			}
		}
	}

	/**
	 * Replace a script with another.
	 *
	 * @param  string $find
	 * @param  string $replace
	 *
	 * @return void
	 */
	public function replace($find, $replace)
	{
		$replace = $this->getScriptArray($replace, 'Argument 2 passed to '.__METHOD__);

		foreach ($this->scripts as $key => $scripts) {
			foreach ($scripts as $innerKey => $script) {
				if ($find == $script[0] || $find == $script[1]) {
					$this->scripts[$key][$innerKey] = $replace;
				}
			}
		}
	}

	/**
	 * Change an existing script's priority.
	 *
	 * @param  string $url
	 * @param  int    $newPriority
	 *
	 * @return void
	 */
	public function setPriority($url, $newPriority)
	{
		foreach ($this->scripts as $key => $scripts) {
			foreach ($scripts as $innerKey => $script) {
				if ($url == $script[0] || $url == $script[1]) {
					unset($this->scripts[$key][$innerKey]);
					$this->scripts[$newPriority][] = $script;
				}
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->all());
	}

	/**
	 * Get all the items in the collection as a flat array.
	 *
	 * @return array
	 */
	public function all()
	{
		if (!$this->scripts) return [];

		krsort($this->scripts);

		return array_map(function($script) {
			return $this->getUrlFromScript($script);
		}, call_user_func_array('array_merge', $this->scripts));
	}

	protected function getUrlFromScript(array $scripts)
	{
		$url = $this->debug ? $scripts[1] : $scripts[0];

		if ((isset($scripts[2]) && $qs = $scripts[2]) || $qs = $this->cacheBuster) {
			$url .= '?'.$qs;
		}

		return $url;
	}
}
