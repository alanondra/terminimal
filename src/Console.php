<?php

namespace Terminimal;

final class Console
{
	const OUT = 1;
	const ERR = 2;

	private static $init = false;
	private static $stdin;
	private static $stdout;
	private static $stderr;
	private static $prefix = null;
	private static $isWin = null;
	private static $nullDevice;

	/**
	 * Initialize stream handles to input and output buffers.
	 *
	 * @return void
	 */
	private static function init()
	{
		if (static::$init) {
			return;
		}

		static::$stdin = fopen('php://stdin', 'r');
		static::$stdout = fopen('php://stdout', 'w');
		static::$stderr = fopen('php://stderr', 'w');

		static::$isWin = (strtolower(substr(PHP_OS, 0, 3)) === 'win');

		static::$nullDevice = ((static::$isWin) ? 'NUL' : '/dev/null');

		static::$init = true;
	}

	/**
	 * Returns the width of the screen in characters.
	 *
	 * @return int
	 */
	public static function getColumns()
	{
		static::init();

		$cols = shell_exec(sprintf('tput cols 2> %s', static::$nullDevice));

		if (!is_null($cols)) {
			return intval($cols);
		}

		$con = shell_exec(sprintf('mode con/status'));

		if (!is_null($con)) {
			$lines = explode("\n", trim($con));
			list($label, $value) = array_map('trim', explode(':', $lines[3], 2));
			return intval($value);
		}

		return 0;
	}

	/**
	 * Returns the height of the screen or output buffer in characters.
	 *
	 * @return int
	 */
	public static function getRows()
	{
		static::init();

		$cols = shell_exec(sprintf('tput lines 2> %s', static::$nullDevice));

		if (!is_null($cols)) {
			return intval($cols);
		}

		$con = shell_exec(sprintf('mode con/status'));

		if (!is_null($con)) {
			$lines = explode("\n", trim($con));
			list($label, $value) = array_map('trim', explode(':', $lines[2], 2));
			return intval($value);
		}

		return 0;
	}

	/**
	 * Sets the prefix before all writeLine calls.
	 *
	 * @param  string|null  $prefix
	 */
	public static function setPrefix($prefix)
	{
		if (is_string($prefix) || is_null($prefix)) {
			static::$prefix = $prefix;
		}
	}

	/**
	 * Write a message to the console exactly as passed in.
	 *
	 * @param  string  $message
	 * @param  int  $stream Use either 1 or static::OUT for STDOUT, or 2 or static::ERR for STDERR.
	 */
	public static function write($message, $stream = 1)
	{
		static::init();

		$handle = ($stream === static::ERR)
			? static::$stderr
			: static::$stdout;

		fwrite($handle, $message);
	}

	/**
	 * Write a message to the console, followed by an EOL.
	 *
	 * @param  string  $message
	 * @param  int  $stream Use either 1 or static::OUT for STDOUT, or 2 or static::ERR for STDERR.
	 */
	public static function writeLine($message, $stream = 1)
	{
		static::write(static::$prefix . $message . PHP_EOL, $stream);
	}

	/**
	 * Read a line from input.
	 *
	 * @param  string|null  $hint Optional hint message, does not print EOL.
	 *
	 * @return type
	 */
	public static function readLine($hint = null)
	{
		static::init();

		if (!empty($hint) && is_string($hint)) {
			static::write($hint);
		}

		return fgets(static::$stdin);
	}
}
