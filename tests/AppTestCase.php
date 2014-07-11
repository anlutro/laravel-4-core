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
		$app['config']->set('auth.driver', 'eloquent-exceptions');
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

	protected function checkForMissingTranslations()
	{
		$str = 'c::';
		$response = $this->client->getResponse();

		if ($response->isRedirection()) {
			$sessionData = $this->app['session']->all();
			$sessionData = array_map(function($data) {
				if ($data instanceof \Illuminate\Support\ViewErrorBag) {
					$data = $data->getBag('default')->all();
				}
				return $data;
			}, $sessionData);
			array_walk_recursive($sessionData, function($data) use($str) {
				if ($data) {
					$this->assertNotContains($str, $data);
				}
			});
		} else {
			$this->assertNotContains($str, $response->getContent());
		}
	}
}
