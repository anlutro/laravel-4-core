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

	/**
	 * Set up the test. This is ran before every test.
	 */
	public function setUp()
	{
		parent::setUp();

		// set to sqlite simply because that's the only sql pdo driver I have
		// installed on my dev laptop :)
		$this->app['db']->setDefaultConnection('sqlite');

		// the package's views extends the 'layout.main' view which must be
		// present. if it is not present, add our dummy views
		if (!$this->app['view']->exists('layout.main')) {
			$this->app['view']->addLocation(__DIR__ . '/resources/views');
		}
	}
}
