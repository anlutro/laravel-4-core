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
	protected $debug = false;
	protected $scripts = [];

	public function setGlobalDebug($toggle)
	{
		static::$globalDebug = (bool) $toggle;
	}

	public function __construct($debug = null)
	{
		$this->setDebug($debug);
	}

	public function setDebug($toggle)
	{
		if ($toggle === null) {
			$this->debug = static::$globalDebug;
		} else {
			$this->debug = (bool) $toggle;
		}
	}

	/**
	 * Add a script to the collection.
	 *
	 * @param array|string  $url      Array of [url, devUrl] or just a single url that counts as both
	 * @param integer       $priority Larger number = higher priority = comes before other scripts
	 */
	public function add($url, $priority = 0)
	{
		if (is_string($url)) {
			$url = [$url, $url];
		} else if (!is_array($url)) {
			$message = 'Argument 1 passed to '.__METHOD__.' must be of the type string or array, '.gettype($url).' given';
			throw new \InvalidArgumentException($message);
		}

		if (!is_integer($priority)) {
			$message = 'Argument 2 passed to '.__METHOD__.' must be of the type integer, '.gettype($priority).' given';
			throw new \InvalidArgumentException($message);
		}

		$this->scripts[$priority][] = $url;
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
		krsort($this->scripts);

		return array_map(function($script) {
			return $this->debug ? $script[1] : $script[0];
		}, call_user_func_array('array_merge', $this->scripts));
	}
}
