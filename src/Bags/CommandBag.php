<?php

namespace Terminimal\Bags;

use Exception;
use Terminimal\Command;

class CommandBag
{
	protected $commands;

	public function __construct()
	{
		$this->commands = [];
	}

	public function all()
	{
		return array_keys($this->commands);
	}

	public function set($handle, $command)
	{
		if (!is_string($handle)) {
			$type = ( is_object($handle) ? get_class($handle) : gettype($handle) );
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

		$_handle = $this->formatHandle($handle);

		$this->commands[$_handle] = $command;

		return $this;
	}

	public function with(array $commands)
	{
		foreach ($commands as $handle => $command) {
			$this->set($handle, $command);
		}

		return $this;
	}

	public function get($handle)
	{
		$_handle = $this->formatHandle($handle);

		return (key_exists($_handle, $this->commands) ? $this->commands[$_handle] : null);
	}

	public function remove($handle)
	{
		$_handle = $this->formatHandle($handle);

		if (key_exists($_handle, $this->commands)) {
			unset($this->commands[$_handle]);
		}

		return $this;
	}

	public function without(array $handles)
	{
		foreach ($handles as $handle) {
			$this->remove($handle);
		}

		return $this;
	}

	protected function formatHandle($handle)
	{
		if (!is_null($handle) && !is_string($handle)) {
			$type = ( is_object($handle) ? get_class($handle) : gettype($handle) );
			throw new Exception(sprintf('Command handle must be a string; %s given.', $type));
		}

		return strtolower(trim($handle));
	}
}
