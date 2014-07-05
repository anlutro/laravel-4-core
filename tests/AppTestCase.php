<?php
namespace anlutro\Core\Tests;

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
		return [
			'anlutro\Menu\ServiceProvider',
			'anlutro\Core\CoreServiceProvider',
		];
	}

	public function createApplication()
	{
		$app = parent::createApplication();

		// set to sqlite simply because that's the only sql pdo driver I have
		// installed on my dev laptop :)
		$app['db']->setDefaultConnection('sqlite');

		$app['config']->set('database.connections.sqlite.database', ':memory:');
		$app['config']->set('auth.model', 'anlutro\Core\Auth\Users\UserModel');

		return $app;
	}

	/**
	 * Set up the test. This is ran before every test.
	 */
	public function setUp()
	{
		parent::setUp();

		$this->app['view']->alias('c::layout.main-nosidebar', 'c::layout.main');
	}
}
