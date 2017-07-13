<?php

namespace Terminimal\Commands;

use Terminimal\Command;
use Terminimal\Application;

class DefaultCommand extends Command
{
	/**
	 * Condition to check if the Command should display its manual instead of executing.
	 *
	 * @return boolean
	 */
	public function shouldShowManual()
	{
		return false;
	}

	/**
	 * Run the script.
	 */
	public function run()
	{
		$console = $this->app->console;

		$commands = array_values(array_filter($this->app->commands->all(), function ($command) {
			return $command != Application::DEFAULT_HANDLE;
		}));

		if (!empty($commands)) {
			$console->out('Available commands:');

			foreach ($commands as $name) {
				$console->out(sprintf(' - %s', $name));
			}
		} else {
			$console->out('No commands are registered.');
		}
	}
}
