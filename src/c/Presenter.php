<?php
/**
 * Laravel 4 Core
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

abstract class Presenter
{
	protected $object;

	public function __construct($object)
	{
		$this->object = $object;
	}

	private function hasPresenterMethod($key)
	{
		$name = 'present' . Str::studly($key);

		if (method_exists($this, $name)) {
			return $name;
		} else {
			return false;
		}
	}

	private function callPresenterMethod($key, $method)
	{
		$params = [];

		if (isset($this->object->$key)) {
			$params = [$this->object->$key];
		}

		return call_user_func_array([$this, $method], $params);
	}

	public function __get($key)
	{
		if ($method = $this->hasPresenterMethod($key)) {
			return $this->callPresenterMethod($key, $method);
		}

		return $this->object->$key;
	}

	public function __set($key, $value)
	{
		throw new \RuntimeException('Cannot set variables on ' . get_class($this));
	}

	public static function make($value)
	{
		// new static doesn't seem to work?
		$class = get_called_class();

		if ($value instanceof PresentableInterface) {
			return $value->makePresenter();
		} elseif ($value instanceof Collection) {
			return $value->transform(function($item) use($class) {
				return new $class($item);
			});
		} elseif (!is_object($value)) {
			$value = json_decode(json_encode($value));
		}

		return new $class($value);
	}
}
