<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c\Auth\Console;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Abstract command for managing users via the console.
 */
abstract class AbstractUserManagementCommand extends \c\Command
{
	/**
	 * Get a user based on the input provided to the command.
	 *
	 * @return mixed
	 */
	public function getUser()
	{
		$class = $this->laravel['config']->get('auth.model');
		$model = $this->laravel->make($class);
		$query = $model->newQuery();

		foreach ($this->option() as $key => $value) {
			if (!empty($value)) $query->where($key, '=', $value);
		}

		if (empty($query->wheres)) {
			$this->error('Must provide at least one option! (username, email or id)');
			exit(1);
		}

		$count = $query->count();
		if ($count < 1) {
			$this->error('No users with those credentials found.');
			exit(1);
		} elseif ($count > 1) {
			$this->error('More than 1 user with those credentials found. Please be more specific.');
			exit(1);
		}

		return $query->first();
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
			array('username', null, InputOption::VALUE_OPTIONAL, 'Username to look for.', null),
			array('email', null, InputOption::VALUE_OPTIONAL, 'E-mail address to look for.', null),
			array('id', null, InputOption::VALUE_OPTIONAL, 'Database ID to look for.', null),
		);
	}
}
