<?php
/**
 * Laravel 4 Core
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-core
 */

namespace c;

use Illuminate\Console\Command as BaseCommand;

abstract class Command extends BaseCommand
{
	/**
	 * Overwrite the ask function to get rid of that hideous baby blue colour
	 */
	public function ask($question, $default = null)
	{
		$dialog = $this->getHelperSet()->get('dialog');

		return $dialog->ask($this->output, "$question ", $default);
	}
}
