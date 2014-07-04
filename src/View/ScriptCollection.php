<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\View;

use IteratorAggregate;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class ScriptCollection implements IteratorAggregate
{
	protected $scripts = [];

	public function add($script, $priority = 0)
	{
		if (!is_string($script)) {
			throw new \InvalidArgumentException('$script must be a string');
		}

		$priority = (int) $priority;

		$this->scripts[$priority][] = $script;
	}

	public function remove($remove)
	{
		foreach ($this->scripts as $key => $scripts) {
			foreach ($scripts as $innerKey => $script) {
				if ($script == $remove) {
					unset($this->scripts[$key][$innerKey]);
				}
			}
		}
	}

	public function getIterator()
	{
		ksort($this->scripts);

		return new RecursiveIteratorIterator(new RecursiveArrayIterator($this->scripts));
	}

	public function all()
	{
		return iterator_to_array($this->getIterator(), false);
	}
}
