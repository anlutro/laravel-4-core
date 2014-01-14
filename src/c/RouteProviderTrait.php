<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c;

/**
 * Trait for re-usable service provider methods throughout the core package.
 */
trait RouteProviderTrait
{
	/**
	 * Register routes for the pacakage.
	 *
	 * @param  string $file
	 *
	 * @return void
	 */
	protected function registerRoutes($file)
	{
		if (!$this->app['config']->get('c::enable-routes')) return;

		$prefix = $this->app['config']->get('c::route-prefix');

		if ($prefix) {
			$this->app['router']->group(['prefix' => $prefix], function() {
				$this->requireRouteFile($file);
			});
		} else {
			$this->requireRouteFile($file);
		}
	}

	/**
	 * Include the route file for the correct locale.
	 *
	 * @param  string $file
	 *
	 * @return void
	 */
	protected function requireRouteFile($file)
	{
		$locale = $this->app['translator']->getLocale();
		$path = $this->srcPath . '/routes/' . $locale;

		if (!is_dir($path)) {
			$path = $this->srcPath . '/routes/en';
		}

		require $path . '/' . $file . '.php';
	}
}
