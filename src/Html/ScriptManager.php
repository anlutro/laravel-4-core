<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Html;

class ScriptManager
{
	protected $scripts;

	public function __construct($debug = false)
	{
		$this->scripts = [
			'style' => new ScriptCollection($debug),
			'head'  => new ScriptCollection($debug),
			'body'  => new ScriptCollection($debug),
		];
	}

	public function get($name)
	{
		if (!$this->has($name)) {
			throw new \InvalidArgumentException("Script collection with name $name not defined");
		}

		return $this->scripts[$name];
	}

	public function has($name)
	{
		return isset($this->scripts[$name]);
	}

	public function add($name, $url, $priority = 0)
	{
		$this->get($name)->add($url, $priority);
	}

	public function remove($name, $url)
	{
		$this->get($name)->remove($url);
	}

	public function replace($name, $find, $replace)
	{
		$this->get($name)->replace($find, $replace);
	}

	public function setPriority($name, $url, $newPriority)
	{
		$this->get($name)->setPriority($url, $newPriority);
	}
}
