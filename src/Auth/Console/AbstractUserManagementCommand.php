<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Auth\Console;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;

use anlutro\Core\Console\Command;

/**
 * Abstract command for managing users via the console.
 */
abstract class AbstractUserManagementCommand extends Command
{
	/**
	 * Get a user based on the input provided to the command.
	 *
	 * @return mixed
	 */
	public function getUser()
	{
		$class = $this->laravel['config']->get('auth.model');
		/** @var \Illuminate\Database\Eloquent\Model $model */
		$model = $this->laravel->make($class);
		$query = $model->newQuery();

		$options = ['username', 'email', 'id'];
		$optionProvided = false;

		foreach ($options as $key) {
			$value = $this->option($key);
			if (!empty($value)) {
				$optionProvided = true;
				$query->where($key, '=', $value);
			}
		}

		if (!$optionProvided) {
			$this->error('Must provide at least one option! (username, email or id)');
			$this->displayHelp();
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

	protected function displayHelp()
	{
		$this->getNativeDefinition()->setArguments($this->getArguments());
		$helpCommand = $this->getApplication()->get('help');
		$helpCommand->run(new ArrayInput(['command_name' => $this->getName()]), $this->output);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['username', null, InputOption::VALUE_OPTIONAL, 'Username to look for.', null],
			['email', null, InputOption::VALUE_OPTIONAL, 'E-mail address to look for.', null],
			['id', null, InputOption::VALUE_OPTIONAL, 'Database ID to look for.', null],
		];
	}
}
