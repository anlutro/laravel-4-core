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
	protected $defer = false;

	public function register()
	{
		$this->commands('c\Auth\Console\ActivateUserCommand');
		
		$this->app->bindShared('auth.activation.repository', function($app) {
			return new DatabaseActivationCodeRepository($app['db']->connection(), 'user_activation');
		});

		$this->app->bindShared('auth.activation', function($app) {

			$codes = $app['auth.activation.repository'];
			$users = $app['auth']->driver()->getProvider();
			$mailer = $app['mailer'];
			$hashKey = $app['config']->get('app.key');
			$queue = $app['config']->get('auth.reminders.queue', false);

			return new ActivationService($codes, $users, $mailer, $hashKey, $queue);

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
