<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core;

/**
 * Trait for re-usable service provider methods throughout the core package.
 *
 * @property \Illuminate\Foundation\Application $app
 *
 * @todo PHPdoc for static property $resPath
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
			$this->app['router']->group(['prefix' => $prefix], function() use($file) {
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
		$path = static::$resPath . '/routes/' . $locale;

		if (!is_dir($path)) {
			$path = static::$resPath . '/routes/en';
		}

		require $path . '/' . $file . '.php';
	}
}
