<?php

namespace Terminimal\Commands;

use Terminimal\Command;

class DefaultCommand extends Command
{
	/**
	 * Condition to check if the Command should display its manual instead of executing.
	 *
	 * @return boolean
	 */
	public function showsManual()
	{
		return false;
	}

	/**
	 * Run the script.
	 */
	public function run()
	{
		$commands = $this->app->getRoutes();

		if (!empty($commands)) {
			$this->app->console->out('Available commands:');

			foreach ($commands as $name) {
				$this->app->console->out(sprintf(' - %s', $name));
			}
		} else {
			$this->app->console->out('No commands are registered.');
		}
	}
}
