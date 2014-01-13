<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c;

class EloquentBuilder extends \Illuminate\Database\Eloquent\Builder
{
	/**
	 * Generate the fully qualified column name for a column by prepending the
	 * builder's model's table name.
	 *
	 * @param  string $column
	 *
	 * @return string
	 */
	protected function wrap($column)
	{
		return $this->model->getTable() . '.' . $column;
	}
}
