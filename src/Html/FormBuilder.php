<?php
namespace anlutro\Core\Html;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Html\FormBuilder as BaseFormBuilder;

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
		$key = $this->transformKey($name);

		$data = $this->model;

		foreach (explode('.', $key) as $segment) {
			if (is_array($data)) {
				$data = array_get($data, $segment);
			} elseif ($data instanceof Collection) {
				$data = $data->find($segment);
			} elseif ($data instanceof \ArrayAccess) {
				if (!$data->offsetExists($segment)) return;
				$data = $data->offsetGet($segment);
			} elseif (is_object($data)) {
				$data = object_get($data, $segment);
			} else {
				return $data;
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
