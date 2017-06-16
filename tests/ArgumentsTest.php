<?php

namespace TerminimalTests;

use Terminimal\Containers\ArgumentContainer;

class ArgumentsTest extends Test
{
	public function testHasAllComponents()
	{
		$cl = static::fakeCommandLine('test -a -bc -d baz --foo=bar');

		$args = ArgumentContainer::parse($cl);

		$this->assertEquals('test.php', $args->getScript());
		$this->assertEquals('test', $args->getCommand());
		$this->assertEquals(true, $args->hasFlag('a'));
		$this->assertEquals(true, $args->hasFlag('b'));
		$this->assertEquals(true, $args->hasFlag('c'));
		$this->assertEquals(false, $args->hasFlag('d'));
		$this->assertEquals(true, $args->hasAllFlags('abc'));
		$this->assertEquals(false, $args->hasAllFlags('abcd'));
		$this->assertEquals('baz', $args->getOption('d'));
		$this->assertEquals('bar', $args->getOption('foo'));
	}
}
