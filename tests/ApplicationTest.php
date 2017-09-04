<?php

namespace TerminimalTests;

use League\CLImate\CLImate;

use Terminimal\Application;
use Terminimal\Bags\ArgumentBag;
use Terminimal\Bags\CommandBag;

use TerminimalTests\Commands\HelloWorldCommand;

class ApplicationTest extends Test
{
	/**
	 * Test if a generic Application can be created and run.
	 */
	public function testCanCreateEmptyApplication()
	{
		$cl = static::fakeCommandLine('');

		$app = new Application($cl);

		$this->assertInstanceOf(Application::class, $app);
		$this->assertInstanceOf(ArgumentBag::class, $app->arguments);
		$this->assertInstanceOf(CommandBag::class, $app->commands);
		$this->assertInstanceOf(CLImate::class, $app->console);
	}

	/**
	 * Test if a generic Application can be run with a generic Command.
	 */
	public function testHelloWorldCommand()
	{
		$command = 'hello-world';

		$cl = static::fakeCommandLine($command);

		$app = new Application($cl);

		$app->console->output->defaultTo('buffer');

		$app->commands->set($command, HelloWorldCommand::class);

		$app->run();

		$this->assertEquals('Hello, world!' . PHP_EOL, $app->console->output->get('buffer')->get());
	}

	/**
	 * Test if a generic Application can access a generic Command's manual.
	 */
	public function testHelloWorldHelp()
	{
		$flags = ['-h', '--help', '-?'];

		$command = 'hello-world';

		$cl = static::fakeCommandLine(sprintf('%s %s', $command, $flags[rand(0, count($flags)-1)]));

		$app = new Application($cl);

		$app->console->output->defaultTo('buffer');

		$app->commands->set($command, HelloWorldCommand::class);

		$app->run();

		$this->assertEquals('Help text.' . PHP_EOL, $app->console->output->get('buffer')->get());
	}
}
