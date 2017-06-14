<?php

namespace Terminimal;

use Exception;
use Terminimal\Containers\ArgumentContainer;

class Application
{
	protected $arguments;
	protected $commands;

	/**
	 * Create a new instance of Application.
	 *
	 * @param  array  $argv The global $argv variable.
	 *
	 * @return void
	 */
	public function __construct($argv)
	{
		$this->arguments = ArgumentContainer::parse($argv);
		$this->commands = [];
	}

	/**
	 * Associate an extension of Command with a text command.
	 *
	 * @param  string  $route
	 * @param  string  $command
	 *
	 * @return $this
	 *
	 * @throws Exception
	 */
	public function registerCommand($route, $command)
	{
		if (!is_string($route)) {
			$type = ( is_object($route) ? get_class($route) : gettype($route) );
			throw new Exception(sprintf('%s requires a command name string; %s given.', __METHOD__, $type));
		}

		if (!is_string($command)) {
			$type = ( is_object($command) ? get_class($command) : gettype($command) );
			throw new Exception(sprintf('%s requires a class path string; %s given.', __METHOD__, $type));
		}

		if (!class_exists($command)) {
			throw new Exception(sprintf('Class "%s" does not exist.', $command));
		}

		if (!is_subclass_of($command, Command::class)) {
			throw new Exception(sprintf('Class "%s" must be a child of "%s".', $command, Command::class));
		}

		$commandName = strtolower(trim($route));

		$this->commands[$commandName] = $command;

		return $this;
	}

	/**
	 * Associate extensions of Command with text commands.
	 *
	 * @param  array  $commands  Keys representing the routes and values consisting of the class name.
	 *
	 * @return $this
	 */
	public function registerCommands(array $commands)
	{
		foreach ($commands as $route => $command) {
			$this->registerCommand($route, $command);
		}
		return $this;
	}

	/**
	 * Run the application.
	 *
	 * @return void
	 */
	public function run()
	{
		$command = strtolower(trim($this->arguments->getCommand()));

		$valid = $this->validateCommand($command);

		if (!$valid) {
			$this->listCommands();
			exit(1);
		}

		$class = $this->commands[$command];

		$cmd = new $class($this->arguments);

		try {
			$this->renderManual($cmd);

			$run = $cmd->run();

			if ($run === false) {
				exit(1);
			}
		} catch (Exception $exc) {
			Console::writeLine($exc->getMessage(), Console::ERR);
			exit(1);
		}
	}

	/**
	 * Validate that the text command given is registered.
	 *
	 * @param  string  $command
	 *
	 * @return boolean
	 */
	protected function validateCommand($command)
	{
		if (empty($command)) {
			Console::writeLine('Command not specified.' . PHP_EOL, Console::ERR);
			return false;
		}

		if (!key_exists($command, $this->commands)) {
			Console::writeLine('Invalid command specified.', Console::ERR);
			return false;
		}

		return true;
	}

	/**
	 * List all available commands.
	 *
	 * @return $this
	 */
	protected function listCommands()
	{
		if (!empty($this->commands)) {
			Console::writeLine('Available commands (use -? for more info):');

			foreach (array_keys($this->commands) as $name) {
				Console::writeLine(sprintf(' - %s', $name));
			}
		} else {
			Console::writeLine('No commands are registered.');
		}

		return $this;
	}

	/**
	 * Render the Command's manual and exit if the parameter was given.
	 *
	 * @param Command $command
	 *
	 * @return void
	 */
	protected function renderManual(Command $command)
	{
		if ($this->arguments->hasFlag('?') || $this->arguments->hasFlag('h') || $this->arguments->getOption('help')) {
			$man = $command->getManual();
			Console::writeLine($man);
			exit;
		}
	}
}
