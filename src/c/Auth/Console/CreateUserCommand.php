<?php
/**
 * Laravel 4 Core - Create user command
 *
 * @author    Andreas Lutro <anlutro@gmail.com>
 * @license   http://opensource.org/licenses/MIT
 * @package   Laravel 4 Core
 */

namespace c\Auth\Console;

use c\Auth\UserRepository;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateUserCommand extends \c\Command
{

	protected $name = 'auth:createuser';
	protected $description = 'Create a new user.';
	protected $users;

	public function __construct(UserRepository $users)
	{
		parent::__construct();
		$this->users = $users;
	}

	public function fire()
	{
		$attributes = [];
		$attributes['username'] = $this->ask('Username:');
		$attributes['name'] = $attributes['username'];
		$attributes['password'] = $this->secret('Password:');
		$attributes['password_confirmation'] = $this->secret('Confirm password:');
		$attributes['email'] = $this->ask('E-mail address:');

		$activate = $this->confirm('Should the user be activated? If no, an email with activation instructions will be sent. [yes|no]', true);

		if ($user = $this->users->create($attributes, $activate)) {
			$this->info("User created!");
		} else {
			foreach ($this->users->errors()->toArray() as $error) {
				$this->error($error);
			}
			return 1;
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
		);
	}

}
