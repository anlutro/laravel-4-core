<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Html;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Html\FormBuilder as BaseFormBuilder;

/**
 * Improved form builder.
 */
class FormBuilder extends BaseFormBuilder
{
	/**
	 * Shortcut to getValueAttribute.
	 *
	 * @see getValueAttribute
	 */
	public function value($name)
	{
		return $this->getValueAttribute($name);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function checkable($type, $name, $value, $checked, $options)
	{
		// this if statement is not in the original form builder
		if ($checked === null) {
			$checked = $this->getCheckedState($type, $name, $value, $checked);
		}

		if ($checked) $options['checked'] = 'checked';

		return $this->input($type, $name, $value, $options);
	}

	/**
	 * Returns the 'checked' string if the input with the given name is checked.
	 *
	 * @param  string $name
	 *
	 * @return string
	 */
	public function checked($name)
	{
		if ($this->getCheckedState($name)) {
			return 'checked';
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getModelValueAttribute($name)
	{
		$segments = explode('.', $this->transformKey($name));
		$data = $this->model;

		foreach ($segments as $key) {
			if (is_array($data)) {
				$data = array_key_exists($key, $data) ? $data[$key] : null;
			} elseif ($data instanceof Collection) {
				$data = $data->find($key);
			} elseif ($data instanceof \ArrayAccess) {
				$data = $data->offsetExists($key) ? $data->offsetGet($key) : null;
			} elseif (is_object($data)) {
				$data = isset($data->$key) ? $data->$key : null;
			} else {
				return null;
			}
		}

		return $data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIdAttribute($name, $attributes)
	{
		// always add id to the inputs, even if there is no label present.
		return array_key_exists('id', $attributes) ? $attributes['id'] : $name;
	}
}
