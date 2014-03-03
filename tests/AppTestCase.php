<?php

/**
 * Test case that boots the whole application.
 */
class AppTestCase extends \c\L4TestCase
{
	/**
	 * Create the application.
	 *
	 * @return Illuminate\Foundation\Application
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

		$app = new Illuminate\Foundation\Application;
		$env = $app->detectEnvironment(function() { return 'testing'; });
		$app->bindInstallPaths(require __DIR__.'/../vendor/laravel/laravel/bootstrap/paths.php');
		require Illuminate\Foundation\Application::getBootstrapFile();
		return $app;
	}

	/**
	 * Refresh the application instance.
	 *
	 * @return void
	 */
	protected function refreshApplication()
	{
		$this->app = $this->createApplication();

		$this->client = $this->createClient();

		$this->app->setRequestForConsoleEnvironment();

		// every test will be testing stuff that depends on the coreservice-
		// provider, so just register it.
		$this->app->register('c\CoreServiceProvider');

		// allow registration of extra service providers before boot is
		// called, as some providers rely on others being loaded in time.
		foreach ($this->getExtraProviders() as $provider) {
			$this->app->register($provider);
		}

		$this->app->boot();
	}

	/**
	 * Set up the test. This is ran before every test.
	 */
	public function setUp()
	{
		parent::setUp();

		// set to sqlite simply because that's the only sql pdo driver I have
		// installed on my dev laptop :)
		$this->app['db']->setDefaultConnection('sqlite');

		// the package depends on some views, if these are missing we need to
		// fix that by adding some dummy views
		$this->addMissingViews();
	}

	/**
	 * Check for missing views and make them available if necessary.
	 */
	protected function addMissingViews()
	{
		if (
			!$this->app['view']->exists('layout.main') ||
			!$this->app['view']->exists('layout.fullwidth')
		) {
			$this->app['view']->addLocation(__DIR__ . '/resources/views');
		}
	}

	protected function getExtraProviders()
	{
		return [];
	}
}
