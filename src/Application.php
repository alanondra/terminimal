<?php

namespace Terminimal;

use Exception;
use League\CLImate\CLImate;
use Terminimal\Commands\DefaultCommand;
use Terminimal\Containers\ArgumentContainer;

/**
 * Terminimal Application
 *
 * @property-read League\CLImate\CLImate $console
 */
class Application
{
	protected $arguments;
	protected $console;
	protected $commands;
	protected $running;

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
		$this->console = new CLImate();
		$this->commands = [];
		$this->running = false;
		$this->registerCommand('_default', DefaultCommand::class);
	}

	/**
	 * Access non-public properties.
	 *
	 * @param string $property
	 *
	 * @return mixed
	 */
	public function __get($property)
	{
		switch ($property) {
			case 'console':
				return $this->$property;
		}

		return null;
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
	 * Get a list of available command routes.
	 *
	 * @return array
	 */
	public function getRoutes()
	{
		return array_values(array_filter(array_keys($this->commands), function ($command) {
			return $command != '_default';
		}));
	}

	/**
	 * Run the application.
	 *
	 * @return void
	 */
	public function run()
	{
		if ($this->running) {
			return;
		}

		$this->running = true;

		$route = $this->arguments->getCommand();

		$class = $this->findCommand($route);

		try {
			$cmd = new $class($this, $this->arguments);

			if ($cmd->showsManual()) {
				$this->console->out($cmd->getManual());
			} else {
				$cmd->run();
				return true;
			}
		} catch (Exception $exc) {
			$this->console->out($exc->getMessage());
			return false;
		}
	}

	/**
	 * Validate that the text command given is registered.
	 *
	 * @param  string  $route
	 *
	 * @return boolean
	 */
	protected function findCommand($route)
	{
		return $this->commands[(key_exists($route, $this->commands) ? $route : '_default')];
	}
}
