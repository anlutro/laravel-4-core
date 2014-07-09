<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Html;

use Illuminate\Html\HtmlServiceProvider as BaseServiceProvider;

/**
 * Replace the default Illuminate\Html\HtmlServiceProvider with this one in
 * app/config/app.php's providers array to get the improved form builder.
 */
class ServiceProvider extends BaseServiceProvider
{
	/**
	 * Overwrite the form builder binding.
	 *
	 * @return void
	 */
	protected function registerFormBuilder()
	{
		$this->app->bindShared('form', function($app)
		{
			$form = new FormBuilder($app['html'], $app['url'], $app['session.store']->getToken());

			return $form->setSessionStore($app['session.store']);
		});
	}
}
