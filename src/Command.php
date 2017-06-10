<?php

namespace Terminimal;

use Terminimal\Support\ArgumentBag;

abstract class Command
{
	protected $arguments;

	public function __construct(ArgumentBag $arguments)
	{
		$this->arguments = $arguments;
	}

	/**
	 * Manual text when given either -? -h or --help options.
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
