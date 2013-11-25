<?php
if (!function_exists('array_filter_keys')):
function array_filter_keys($array, callable $callback) {
	$keys = array_keys($array);

	$allowed = array_filter($keys, $callback);

	return array_intersect_key($array, array_flip($allowed));
}
endif;
