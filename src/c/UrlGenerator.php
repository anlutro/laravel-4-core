<?php
namespace c;

class UrlGenerator extends \Illuminate\Routing\UrlGenerator
{
	protected function toRoute($route, $parameters)
	{
		// dd($route, $parameters);
		$domain = $this->getRouteDomain($route, $parameters);

		$path = preg_replace_sub('/\{.*?\}/', $parameters, $route->uri());

		$url = $this->trimUrl($this->getRouteRoot($route, $domain), $path);

		if ($query = $this->getQueryString($parameters)) {
			$url .= $query;
		}

		return $url;
	}

	/**
	 * Extract a query string from an array of parameters.
	 *
	 * @param  array  $parameters
	 *
	 * @return string
	 */
	protected function getQueryString(array $parameters)
	{
		if (empty($parameters)) {
			return '';
		}

		// first, we'll get the members of the array that have a string key -
		// these will be transformed into query strings such as ?foo=bar.
		$legal = array_flip(array_filter(array_keys($parameters), function($key) {
			return !is_numeric($key);
		}));

		$query = array_intersect_key($parameters, $legal);

		$str = '';

		if (!empty($query)) {
			$str .= '?' . http_build_query($query);
		}

		// next, get the members of the array that have a numeric key - these
		// will be appended as query strings without a value, i.e. ?baz.
		if ($extra = array_diff_key($parameters, $legal)) {
			$str .= ($str === '' ? '?' : '&') . implode('&', $extra);
		}

		return $str;
	}
}
