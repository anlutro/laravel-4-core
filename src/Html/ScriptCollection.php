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

class ScriptCollection implements IteratorAggregate
{
	protected $scripts = [];

	public function add($url, $priority = 0)
	{
		if (!is_string($url)) {
			throw new \InvalidArgumentException('$url must be a string');
		}

		$priority = (int) $priority;

		$this->scripts[$priority][] = $url;
	}

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

	public function getIterator()
	{
		krsort($this->scripts);

		return new RecursiveIteratorIterator(new RecursiveArrayIterator($this->scripts));
	}

	public function all()
	{
		return iterator_to_array($this->getIterator(), false);
	}
}
