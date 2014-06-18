<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Eloquent;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;

class Builder extends BaseBuilder
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
