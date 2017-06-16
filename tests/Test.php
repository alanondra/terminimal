<?php

namespace TerminimalTests;

use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
	/**
	 * Generate a fake set of command line arguments.
	 *
	 * @param  string  $str  List of arguments as you would type them in the terminal.
	 * @param  string  $script  Name of the 'script' receiving the arguments.
	 *
	 * @return array
	 */
	protected static function fakeCommandLine($str, $script = 'test.php')
	{
		return explode(' ', preg_replace('/\s+/', ' ', trim(sprintf('%s %s', $script, $str))));
	}
}
