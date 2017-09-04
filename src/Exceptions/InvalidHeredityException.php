<?php

namespace Terminimal\Exceptions;

use Exception;
use LogicException;

class InvalidHeredityException extends LogicException
{
	public function __construct($expected, $actual, $code = 0, Exception $previous = null)
	{
		parent::__construct(sprintf('Class "%s" must be a child of "%s".', $actual, $expected), $code, $previous);
	}
}
