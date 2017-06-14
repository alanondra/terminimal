<?php

namespace Terminimal\Commands;

use Terminimal\Command;
use Terminimal\Console;

class DefaultCommand extends Command
{
	/**
	 * Condition to check if the Command should display its manual instead of executing.
	 *
	 * @return boolean
	 */
	public function shouldShowManual() {
		return false;
	}

	/**
	 * Run the script.
	 */
	public function run()
	{
		$commands = $this->app->getRoutes();

		if (!empty($commands)) {
			Console::writeLine('Available commands:');

			foreach ($commands as $name) {
				Console::writeLine(sprintf(' - %s', $name));
			}
		} else {
			Console::writeLine('No commands are registered.');
		}
	}
}
