<?php

namespace TerminimalTests\Commands;

use Terminimal\Command;

class HelloWorldCommand extends Command
{
	public function getManual() {
		return 'Help text.';
	}

	public function run()
	{
		$this->app->console->out('Hello, world!');
	}
}
