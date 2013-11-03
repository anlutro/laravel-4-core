<?php
namespace c\Auth\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateUserCommand extends Command {

	protected $name = 'user:create';
	protected $description = 'Create a new user.';

	protected $users;

	public function __construct(UserRepository $users)
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

		$user->activate();

		$this->info('User activated!');
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