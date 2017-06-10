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

		static::$init = true;
	}

	public static function getDimensions()
	{
		$dim = new \stdClass();
		$dim->x = -1;
		$dim->y = -1;

		if (!static::$isWin) {
			$dim->x = intval(exec('tput cols'));
			$dim->y = intval(exec('tput lines'));
		} else {
			$stat = explode("\n", trim(exec('mode con/status')));

			foreach ($stat as $line) {
				$ln = preg_replace('/\s+/', ' ', trim($line));

				$cols = array_pad(explode(':', $ln, 2), 2, null);

				$key = trim(strtolower($cols[0]));
				$val = intval(trim($cols[1]));

				if ($key == 'columns') {
					$dim->x = $val;
					if ($dim->y >= 0) {
						break;
					}
				} elseif ($key == 'lines') {
					$dim->y = $val;
					if ($dim->x >= 0) {
						break;
					}
				}
			}
		}

		return $dim;
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

	/**
	 * Clear the screen.
	 */
	public static function clear()
	{
		static::init();

		$cmd = ((static::$isWin) ? 'cls' : 'clear');

		system($cmd);
	}
}
