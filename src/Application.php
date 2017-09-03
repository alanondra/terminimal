<?php

namespace Terminimal;

use Exception;

use League\CLImate\CLImate;

use Terminimal\Bags\CommandBag;
use Terminimal\Bags\ArgumentBag;
use Terminimal\Commands\DefaultCommand;

/**
 * @property-read Terminimal\Bags\ArgumentBag $arguments
 * @property-read Terminimal\Bags\CommandBag $commands
 * @property-read League\CLImate\CLImate $console
 */
class Application
{
	const DEFAULT_HANDLE = '_default';

	/*
	 * static list to encourage extensions to the Application class to
	 * document those extensions as part of the class documentation
	 */
	protected static $aliases = [
		'arguments',
		'commands',
		'console',
	];

	/**
	 * @var boolean
	 */
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
		$this->running = false;

		$this->arguments = ArgumentBag::parse($argv);

		$this->commands = new CommandBag();
		$this->commands->set(static::DEFAULT_HANDLE, DefaultCommand::class);

		$this->console = new CLImate();
	}

	/**
	 * Access specific properties.
	 *
	 * @param string $property
	 *
	 * @return mixed
	 */
	public function __get($property)
	{
		if (in_array($property, static::$aliases)) {
			return $this->$property;
		}

		return null;
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

		$args = $this->arguments;
		$commands = $this->commands;
		$console = $this->console;

		try {
			$route = $args->getCommand() ?: static::DEFAULT_HANDLE;

			$class = $commands->get($route) ?: $commands->get(static::DEFAULT_HANDLE);

			if (empty($class)) {
				throw new Exception('No commands are registered (including default).');
			}

			$cmd = new $class($this, $this->arguments);

			if ($cmd->shouldShowManual()) {
				$console->out($cmd->getManual());
			} else {
				$cmd->run();
			}
		} catch (Exception $exc) {
			$console->error($exc->getMessage());
			exit(1);
		}
	}
}
