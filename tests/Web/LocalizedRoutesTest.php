<?php
namespace anlutro\Core\Tests\Web;

use Mockery as m;
use anlutro\Core\Tests\AppTestCase;

/** @medium */
class LocalizedRoutesTest extends AppTestCase
{
	/**
	 * {@inheritdoc}
	 */
	protected function getExtraProviders()
	{
		return [
			'anlutro\Core\CoreServiceProvider',
			'anlutro\Core\Auth\Reminders\ReminderServiceProvider',
			'anlutro\Core\Auth\Activation\ActivationServiceProvider',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function createApplication()
	{
		$app = parent::createApplication();
		$app->setLocale('no');
		return $app;
	}

	/**
	 * @test
	 * @dataProvider getRouteData
	 */
	public function routesAreLocalized($route, $params, $expected)
	{
		$url = $this->app['url']->route($route, $params);
		$this->assertContains($expected, $url);
	}

	public function getRouteData()
	{
		return [
			['c::login', [], '/bruker/logg-inn'],
			['c::login_post', [], '/bruker/logg-inn'],
			['c::profile', [], '/bruker/profil'],
			['c::profile_post', [], '/bruker/profil'],
			['c::logout', [], '/bruker/logg-ut'],
			['c::user.show', [1], '/bruker/1/profil'],
			['c::user.index', [], '/admin/brukere'],
			['c::user.bulk', [], '/admin/brukere'],
			['c::user.create', [], '/admin/brukere/ny'],
			['c::user.store', [], '/admin/brukere/ny'],
			['c::user.edit', [1], '/admin/brukere/1'],
			['c::user.update', [1], '/admin/brukere/1'],
			['c::user.delete', [1], '/admin/brukere/1'],
			['c::pwreset.request', [], '/passord/glemt'],
			['c::pwreset.request_post', [], '/passord/glemt'],
			['c::pwreset.reset', [], '/passord/tilbakestill'],
			['c::pwreset.reset_post', [], '/passord/tilbakestill'],
			['c::activation.register', [], '/bruker/registrer'],
			['c::activation.register_post', [], '/bruker/registrer'],
			['c::activation.activate', [], '/bruker/aktiver'],
		];
	}
}