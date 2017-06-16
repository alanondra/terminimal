<?php

namespace Terminimal;

use Terminimal\Application;
use Terminimal\Containers\ArgumentContainer;

abstract class Command
{
	/**
	 * @var Terminimal\Application
	 */
	protected $app;
	/**
	 * @var Terminimal\Containers\ArgumentContainer
	 */
	protected $arguments;

	/**
	 * Create a new instance of Command.
	 *
	 * @param  Application  $app
	 * @param  ArgumentContainer  $arguments
	 */
	public function __construct(Application $app, ArgumentContainer $arguments)
	{
		$this->app = $app;
		$this->arguments = $arguments;
	}

	/**
	 * Condition to check if the Command should display its manual instead of executing.
	 *
	 * @return boolean
	 */
	public function showsManual()
	{
		return ($this->arguments->hasFlag('?') || $this->arguments->hasFlag('h') || $this->arguments->getOption('help'));
	}

	/**
	 * Manual text to display if showsManual returns true.
	 *
	 * @return string
	 */
	public function getManual()
	{
		return <<<MAN
This is the default manual. It can be changed by overriding the getManual function in your Command definition.
MAN;
	}

	/**
	 * Run the script.
	 */
	abstract public function run();
}
