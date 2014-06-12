<?php
namespace anlutro\Core\Tests;

use Mockery as m;
use Illuminate\Support\Facades;

class PasswordResetTest extends \AppTestCase
{
	/**
	 * {@inheritdoc}
	 */
	protected function getExtraProviders()
	{
		return [
			'anlutro\Core\CoreServiceProvider',
			'anlutro\Core\Auth\Reminders\ReminderServiceProvider',
		];
	}

	public function createApplication()
	{
		$app = parent::createApplication();
		$providers = $app['config']->get('app.providers');
		$providers = array_filter($providers, function($provider) {
			return strpos($provider, 'Reminder' !== null);
		});
		$app['config']->set('app.providers', $providers);
		$app['config']->set('auth.reminder.email', null);
		return $app;
	}

	public function setUp()
	{
		parent::setUp();
		Facades\Schema::create('password_reminders', function($t) {
			$t->string('email')->index();
			$t->string('token')->index();
			$t->timestamp('created_at');
		});
		(new \UsersCreateTable())->up();
	}

	public function tearDown()
	{
		parent::tearDown();
		m::close();
		(new \UsersCreateTable())->down();
		Facades\Schema::drop('password_reminders');
	}

	/** @test */
	public function createUserAndResetPasswordImmediately()
	{
		// create the user
		$this->be($user = $this->mockUser());
		$user->user_level = 255;
		$manager = $this->app->make('anlutro\Core\Auth\UserManager');
		$user = $manager->create([
			'username' => 'foobar', 'email' => 'foo@bar.com',
			'password' => 'foobar', 'password_confirmation' => 'foobar',
			'name' => 'Foo Bar', 'phone' => '', 'is_active' => '1',
		]);
		$this->assertInstanceOf('anlutro\Core\Auth\UserModel', $user, $manager->getErrors());
		$result = $manager->login(['username' => 'foobar', 'password' => 'foobar']);
		$this->assertEquals(true, $result);

		$this->app->make('mailer')->setSwiftMailer($mock = m::mock('Swift_Mailer'));
		$mock->shouldReceive('send')->once()
			->andReturnUsing(function($msg) use(&$token) {
				$oldEnv = $this->app['env'];
				$this->app['env'] = 'testing';
				$body = $msg->getBody();
				preg_match('/localhost\/reset\?token=([0-9a-z]+)/', $body, $token);
				if (empty($token[1])) {
					$this->fail('Reset token not found in message body: '.$body);
				}
				$token = $token[1];
				$this->app['env'] = $oldEnv;
			});
		$manager->requestPasswordResetForEmail('foo@bar.com');

		$result = $manager->resetPasswordForCredentials(['email' => 'foo@bar.com'], ['password' => 'barfoo', 'password_confirmation' => 'barfoo'], $token);
		$this->assertEquals(true, $result, $manager->getErrors());

		$result = $manager->login(['username' => 'foobar', 'password' => 'foobar']);
		$this->assertEquals(false, $result);
		$result = $manager->login(['username' => 'foobar', 'password' => 'barfoo']);
		$this->assertEquals(true, $result);
	}

	protected function mockUser()
	{
		return m::mock('anlutro\Core\Auth\UserModel')->makePartial();
	}

	public function setMailExpectations()
	{
		
	}
}
