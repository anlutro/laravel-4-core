<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace anlutro\Core\Console;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PublishCommand extends Command
{
	protected $name = 'core:publish';
	protected $description = 'Publish anlutro/l4-core resources.';

	public function __construct(Filesystem $files)
	{
		parent::__construct();
		$this->files = $files;
	}

	public function fire()
	{
		$resourcePath = dirname(dirname(__DIR__)).'/resources';

		$copy = $this->input->getArgument('copy');

		$copy = $copy ? [$copy] : ['config', 'lang', 'migration', 'view'];

		foreach ($copy as $method) {
			$method = 'copy'.ucfirst($method).'Files';
			$this->$method($resourcePath);
		}
	}

	protected function copyDirectory($from, $to)
	{
		$this->output->writeln("Copying from $from to $to");

		if (!$this->files->isDirectory($to)) {
			$this->files->makeDirectory($to, 0755, true);
		}

		$this->files->copyDirectory($from, $to);
	}

	protected function copyConfigFiles($resourcePath)
	{
		$this->output->writeln("<info>Copying config files</info>");

		$sourcePath = $resourcePath.'/config';
		$targetPath = $this->laravel['path.base'].'/app/config/packages/anlutro/l4-core';

		$this->copyDirectory($sourcePath, $targetPath);
	}

	protected function copyLangFiles($resourcePath)
	{
		$this->output->writeln("<info>Copying translation files</info>");

		foreach (['en', 'no'] as $lang) {
			$sourcePath = $resourcePath."/lang/$lang";
			$targetPath = $this->laravel['path.base']."/app/lang/packages/$lang/c";
			$this->copyDirectory($sourcePath, $targetPath);
		}
	}

	protected function copyMigrationFiles($resourcePath)
	{
		$this->output->writeln("<info>Copying migration files</info>");

		$sourcePath = $resourcePath.'/migrations';
		$targetPath = $this->laravel['path.base'].'/app/database/migrations';

		$this->copyDirectory($sourcePath, $targetPath);
	}

	protected function copyViewFiles($resourcePath)
	{
		$this->output->writeln("<info>Copying template files</info>");

		$sourcePath = $resourcePath.'/views';
		$targetPath = $this->laravel['path.base'].'/app/views/packages/anlutro/l4-core';

		$this->copyDirectory($sourcePath, $targetPath);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['copy', InputArgument::OPTIONAL, 'What to copy. "config", "lang", "migration", "view"']
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
	}
}
