<?php

namespace Terminimal\Bags;

class ArgumentBag
{
	/**
	 * Parse the command line in a less restrictive manner than is possible via getopt.
	 *
	 * @param  array  $argv
	 *
	 * @return static
	 */
	public static function parse($argv)
	{
		$arguments = new static();

		$arguments->script = array_shift($argv);
		$arguments->command = array_shift($argv);

		if (!is_null($arguments->command)) {
			$arguments->command = strtolower(trim($arguments->command));
		}

		$regex = array(
			'short' => '/^-([a-z])$/i',
			'flags' => '/^-([a-z\?]+)$/i',
			'options' => '/^--([a-z0-9]+((\.|_|-)[a-z0-9]+)*)(=.+)?/i',
		);

		$matches = null;

		for ($i=0, $l=count($argv); $i<$l; $i++) {
			$arg = $argv[$i];

			// shorthand options: -u <username>
			// single flags if next value matches other groups: -R --user="Dude Guy"
			if (preg_match($regex['short'], $arg, $matches) === 1) {
				$narg = (key_exists($i+1, $argv) ? $argv[$i+1] : false);

				if (empty($narg) || preg_match($regex['short'], $narg) === 1 || preg_match($regex['flags'], $narg) === 1 || preg_match($regex['options'], $narg) === 1) {
					$arguments->flags[$matches[1]] = true;
				} elseif (preg_match($regex['short'], $narg) !== 1 && preg_match($regex['flags'], $narg) !== 1 && preg_match($regex['options'], $narg) !== 1) {
					$arguments->options[$matches[1]] = $narg;
					$i++;
				}
			} elseif (preg_match($regex['flags'], $arg, $matches) === 1) {
				// flags: -rf
				$arguments->flags = array_replace($arguments->flags, array_fill_keys(str_split($matches[1]), true));
			} elseif (preg_match($regex['options'], $arg, $matches) === 1) {
				// full options: --user.name="Dude Guy"
				array_shift($matches);
				$opt = array_shift($matches);
				$value = array_pop($matches);

				if (!empty($value)) {
					$value = substr($value, 1);
				}
				if (empty($value)) {
					$value = true;
				}

				$arguments->options[$opt] = $value;
			} else {
			// everything else: position doesn't matter, unlike getopt
				$arguments->parameters[] = $arg;
			}
		}

		return $arguments;
	}

	private $script;
	private $command;
	private $flags;
	private $options;
	private $parameters;

	/**
	 * Create a new instance of ArgumentBag.
	 */
	public function __construct()
	{
		$this->script = null;
		$this->command = null;
		$this->flags = array();
		$this->options = array();
		$this->parameters = array();
	}

	/**
	 * Gets the name of the script file passed to PHP.
	 *
	 * @return string|null The name of the file, or null if $argv hasn't been parsed yet.
	 */
	public function getScript()
	{
		return $this->script;
	}

	/**
	 * Gets the first argument passed to PHP immediately after the name of the script.
	 *
	 * @return string|null The name of the command, or null if $argv hasn't been parsed yet.
	 */
	public function getCommand()
	{
		return $this->command;
	}

	/**
	 * Gets all flags as an array.
	 *
	 * @param  string  $flags
	 *
	 * @return array
	 */
	public function getFlags()
	{
		return array_keys($this->flags);
	}

	/**
	 * Check if a single flag was set.
	 *
	 * @param  string  $flag
	 *
	 * @return boolean
	 */
	public function hasFlag($flag)
	{
		if (strlen($flag) != 1) {
			return false;
		}

		return key_exists($flag, $this->flags);
	}

	/**
	 * Check if all the given flags were set.
	 *
	 * @param  string  $flags
	 *
	 * @return boolean
	 */
	public function hasAllFlags($flags)
	{
		foreach (str_split($flags) as $flag) {
			if (!key_exists($flag, $this->flags)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the value of a command line option.
	 *
	 * @param  string  $option Name of the command line option.
	 * @param  mixed  $default Default value if the option wasn't set (default: null).
	 *
	 * @return mixed
	 */
	public function getOption($option, $default = false)
	{
		return (key_exists($option, $this->options) ? $this->options[$option] : $default);
	}

	/**
	 * Gets the list of parameters which weren't flags or options.
	 *
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}
}
