<?php
namespace anlutro\Core\Tests\Auth\Reminders;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Mail\Message;
use Mockery as m;
use Illuminate\Support\Facades;
use anlutro\Core\Tests\AppTestCase;
use anlutro\Core\Auth\AuthenticationException;

class PasswordResetTest extends AppTestCase
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
		$app['config']->set('auth.reminder.email', null);
		return $app;
	}

	public function setUp()
	{
		parent::setUp();
		Facades\Schema::create('password_reminders', function(Blueprint $t) {
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
		/** @var \anlutro\Core\Auth\UserManager $manager */
		$manager = $this->app->make('anlutro\Core\Auth\UserManager');
		$user = $manager->create([
			'username' => 'foobar', 'email' => 'foo@bar.com',
			'password' => 'foobar', 'password_confirmation' => 'foobar',
			'name' => 'Foo Bar', 'phone' => '', 'is_active' => '1',
		]);
		$this->assertInstanceOf('anlutro\Core\Auth\Users\UserModel', $user, $manager->getErrors());
		$result = $manager->login(['username' => 'foobar', 'password' => 'foobar']);
		$this->assertEquals(true, $result);

		$this->app->make('mailer')->setSwiftMailer($mock = m::mock('Swift_Mailer'));
		$mock->shouldReceive('send')->once()
			->andReturnUsing(function($msg) use(&$token) {
				$oldEnv = $this->app['env'];
				$this->app['env'] = 'testing';
				$body = $msg->getBody();
				preg_match('/localhost\/password\/reset\?token=([0-9a-z]+)/', $body, $token);
				if (empty($token[1])) {
					$this->fail('Reset token not found in message body: '.$body);
				}
				$token = $token[1];
				$this->app['env'] = $oldEnv;
			});
		$manager->requestPasswordResetForEmail('foo@bar.com');

		$result = $manager->resetPasswordForCredentials(['email' => 'foo@bar.com'], ['password' => 'barfoo', 'password_confirmation' => 'barfoo'], $token);
		$this->assertEquals(true, $result, $manager->getErrors());

		try {
			$manager->login(['username' => 'foobar', 'password' => 'foobar']);
		} catch (AuthenticationException $e) {
			$this->assertEquals('Incorrect password', $e->getMessage());
		}

		$result = $manager->login(['username' => 'foobar', 'password' => 'barfoo']);
		$this->assertEquals(true, $result);
	}

	protected function mockUser()
	{
		return m::mock('anlutro\Core\Auth\Users\UserModel')->makePartial();
	}

	public function setMailExpectations()
	{
		
	}
}
