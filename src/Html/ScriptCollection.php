<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Html;

use IteratorAggregate;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * Collection for holding HTML scripts such as CSS and JS files.
 */
class ScriptCollection implements IteratorAggregate
{
	protected $scripts = [];

	/**
	 * Add a script to the collection.
	 *
	 * @param string  $url
	 * @param integer $priority Larger number = higher priority = comes before other scripts
	 */
	public function add($url, $priority = 0)
	{
		if (!is_string($url)) {
			$message = 'Argument 1 passed to '.__METHOD__.' must be of the type string, '.gettype($url).' given';
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
				if ($script == $url) {
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
		krsort($this->scripts);

		return new RecursiveIteratorIterator(new RecursiveArrayIterator($this->scripts));
	}

	/**
	 * Get all the items in the collection as a flat array.
	 *
	 * @return array
	 */
	public function all()
	{
		return iterator_to_array($this->getIterator(), false);
	}
}
