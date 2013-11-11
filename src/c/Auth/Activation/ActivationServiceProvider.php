<?php
/**
 * Laravel 4 Core - User activation service provider
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth\Activation;

use Illuminate\Support\ServiceProvider;

class ActivationServiceProvider extends ServiceProvider
{
	public function register()
	{
		$this->commands('c\Auth\Console\ActivateUserCommand');
		
		$this->app->bind('c\Auth\Activation\ActivationCodeRespositoryInterface',
			'c\Auth\Activation\DatabaseActivationCodeRepository');

		$this->app['auth.activation'] = $this->app->share(function($app) {

			// use app.make to allow the end user to easily bind his/her own
			// implementation to the interface
			$codes = $app->make('c\Auth\Activation\ActivationCodeRespositoryInterface');

			$users = $app['auth']->driver()->getProvider();
			$mailer = $app['mailer'];
			$hashKey = $app['config']->get('app.key');

			return new ActivationService($codes, $users, $mailer, $hashKey);

		});
	}

	public function boot()
	{
		$this->srcPath = __DIR__ . '/../../..';
		$this->requireRouteFile('activation');
	}

	protected function requireRouteFile($file)
	{
		$locale = $this->app['translator']->getLocale();

		$path = $this->srcPath . '/routes/' . $locale;

		if (!is_dir($path) || !$locale) {
			$path = $this->srcPath . '/routes/en';
		}

		require $path . '/' . $file . '.php';
	}
}
