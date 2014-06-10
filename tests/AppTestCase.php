<?php

/**
 * Test case that boots the whole application.
 */
class AppTestCase extends \anlutro\LaravelTesting\PkgAppTestCase
{
	/**
	 * {@inheritdoc}
	 */
	public function getVendorPath()
	{
		return __DIR__.'/../vendor';
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getExtraProviders()
	{
		return ['anlutro\Core\CoreServiceProvider'];
	}

	public function createApplication()
	{
		$app = parent::createApplication();

		// set to sqlite simply because that's the only sql pdo driver I have
		// installed on my dev laptop :)
		$app['db']->setDefaultConnection('sqlite');

		$app['config']->set('database.connections.sqlite.database', ':memory:');
		$app['config']->set('auth.model', 'anlutro\Core\Auth\UserModel');

		return $app;
	}

	/**
	 * Set up the test. This is ran before every test.
	 */
	public function setUp()
	{
		parent::setUp();

		// the package's views extends the 'layout.main' view which must be
		// present. if it is not present, add our dummy views
		if (!$this->app['view']->exists('layout.main')) {
			$this->app['view']->addLocation(__DIR__ . '/resources/views');
		}
	}
}
