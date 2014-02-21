<?php
// if (!class_exists('TestCase')) {
// 	class TestCase extends \c\L4TestCase {}
// }

class AppTestCase extends c\L4TestCase
{
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

		$tryfiles = [
			__DIR__ . '/../vendor/laravel/laravel/bootstrap/start.php',
			__DIR__ . '/../../../../../bootstrap/start.php',
			__DIR__ . '/../../../../bootstrap/start.php',
			__DIR__ . '/../../../bootstrap/start.php',
			__DIR__ . '/../../bootstrap/start.php',
			__DIR__ . '/../bootstrap/start.php',
		];

		foreach ($tryfiles as $file) {
			if (file_exists($file)) {
				return require $file;
			}
		}
	}

	public function setUp()
	{
		parent::setUp();
		$this->app['db']->setDefaultConnection('sqlite');
		$this->app['config']->set('auth.model', null);
		$this->app->bind('c\Auth\UserModel', 'c\Auth\UserModel');
		$this->loadCoreProviders();
		$this->addMissingViews();
	}

	private function loadCoreProviders()
	{
		$loaded = $this->app->getLoadedProviders();
		$providers = [
			'c\CoreServiceProvider',
		];

		foreach ($providers as $provider) {
			if (!array_key_exists($provider, $loaded)) {
				$this->app->register($provider);
			}
		}
	}

	public function addMissingViews()
	{
		if (
			!$this->app['view']->exists('layout.main') ||
			!$this->app['view']->exists('layout.fullwidth')
		) {
			$this->app['view']->addLocation(__DIR__ . '/resources/views');
		}
	}
}
