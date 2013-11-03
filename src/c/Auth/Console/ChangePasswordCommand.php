<?php
namespace c\Auth\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ChangePasswordCommand extends Command {

	protected $name = 'user:changepass';
	protected $description = 'Change the password of an existing user.';
	protected $model;

	public function __construct()
	{
		parent::__construct();
		$model = $this->laravel['config']->get('auth.model');
		$this->model = $this->laravel->make($model);
	}

	public function fire()
	{
		$query = $this->model->newQuery();

		foreach ($this->option() as $key => $value) {
			if (!empty($value)) $query->where($key, '=', $value);
		}

		if (empty($query->wheres)) {
			$this->error('Must provide at least one option! (username, email or id)');
			return 1;
		}

		$count = $query->count();
		if ($count < 1) {
			$this->error('No users with those credentials found.');
			return 1;
		} elseif ($count > 1) {
			$this->error('More than 1 user with those credentials found. Please be more specific.');
			return 1;
		}

		$user = $query->first();

		do {
			$password = $this->ask('New password:');
			$confirm = $this->ask('Confirm new password:');
		} while ($password !== $confirm);

		$user->password = $password;
		$user->save();

		$this->info('Password updated!');
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
			array('username', null, InputOption::OPTIONAL, 'Username to look for.', null),
			array('email', null, InputOption::OPTIONAL, 'E-mail address to look for.', null),
			array('id', null, InputOption::OPTIONAL, 'Database ID to look for.', null),
		);
	}

}