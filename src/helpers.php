<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

if (!function_exists('array_filter_keys')):
/**
 * Filter an array by a callback on its keys.
 *
 * @param  array    $array
 * @param  callable $callback
 *
 * @return array
 */
function array_filter_keys($array, callable $callback)
{
	$keys = array_keys($array);

	$allowed = array_filter($keys, $callback);

	return array_intersect_key($array, array_flip($allowed));
}
endif;
